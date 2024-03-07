<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa = $_SESSION['EMPRESA'];
	// if($idCliente == 0 || $idCliente == '' || !isset($idCliente)){ echo 'No existe un cliente seleccionado'; exit; }

	//CONSULTAR LAS RETENCIONES DEL DOCUMENTO PARA MOSTRARLO CHECK EN LA GRILLA
	$sql   = "SELECT  id_retencion FROM $tabla_retenciones WHERE activo=1 AND $id_tabla_retenciones='$id_documento' ";
	$query = mysql_query($sql,$link);

	global $arrayCheckGrilla;
	while ($row=mysql_fetch_array($query)) { $arrayCheckGrilla[$row['id_retencion']]='checked'; }


	if ($opc == 'busquedaTerceroPaginacion') {
		busquedaTerceroPaginacion($id_documento,$modulo,$opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa);
		exit;
	}

	$limit			= '100';

	$sql            = "SELECT COUNT(id) as cont FROM retenciones WHERE activo=1 AND id_empresa='$id_empresa' AND modulo='$modulo' ";
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

 	$sqlCuentas   = "SELECT id,retencion,tipo_retencion,valor,base,cuenta,departamento,ciudad FROM retenciones WHERE activo=1 AND id_empresa = '$id_empresa' AND modulo='$modulo'  LIMIT $limit";

	$queryCuentas = mysql_query($sqlCuentas,$link);
	// $contFilaCuenta=1;
	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;
		$arrayCheckGrilla[$rowCuentas['id']]=($arrayCheckGrilla[$rowCuentas['id']]=='')? '' : $arrayCheckGrilla[$rowCuentas['id']];

		$rowCuentas['valor'] = $rowCuentas['valor']*1;
		$rowCuentas['base']  = $rowCuentas['base']*1;

		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
								<div class="campo0">'.$contFilaCuenta.'</div>
								<div class="campo1" style="width:300px" id="retencion_'.$rowCuentas['retencion'].'" title="'.$rowCuentas['retencion'].'">'.$rowCuentas['retencion'].'</div>
								<div class="campo1" style="width:50px" id="valor_'.$rowCuentas['valor'].'">'.$rowCuentas['valor'].'</div>
								<div class="campo1" style="width:70px" id="base_'.$rowCuentas['base'].'">'.$rowCuentas['base'].'</div>
								<div class="campo1" id="departamento_'.$rowCuentas['departamento'].'" title="'.$rowCuentas['departamento'].'">'.$rowCuentas['departamento'].'</div>
								<div class="campo1" id="ciudad_'.$rowCuentas['ciudad'].'" title="'.$rowCuentas['ciudad'].'">'.$rowCuentas['ciudad'].'</div>

								<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<input type="checkbox"  id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrilla(this,\''.$rowCuentas['id'].'\',\''.$rowCuentas['retencion'].'\',\''.$rowCuentas['valor'].'\',\''.$rowCuentas['tipo_retencion'].'\',\''.$rowCuentas['base'].'\',\''.$rowCuentas['cuenta'].'\')" value="'.$rowCuentas['id'].'">
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

	#bodyTablaBoletas{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 100%;
		height           : calc(100% - 46px);
		background-color : #FFF;
		border-bottom    : 1px solid #d4d4d4;
	}

	#bodyTablaBoletas > div{
		overflow      : hidden;
		height        : 22px;
		border-bottom : 1px solid #d4d4d4;
	}

	#bodyTablaBoletas > div > div { height: 18px; /*background-color : #FFF;*/ padding-top: 4px; }
	#bodyTablaBoletas >  div:hover {background-color: #E3EBFC;}

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
			<div class="campo0">&nbsp;</div>
			<div class="campo1" style="width:300px">Retencion</div>
			<div class="campo1" style="width:50px">Valor</div>
			<div class="campo1" style="width:70px">Base</div>
			<div class="campo1" >Departamento</div>
			<div class="campo1" >Ciudad</div>
			<div class="campo4" >Seleccione</div>
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
	arrayLimitGrilla<?php echo $opcGrillaContable; ?>  = new Array();
	PaginaActual<?php echo $opcGrillaContable; ?> = 1;
	MaxPage<?php echo $opcGrillaContable; ?>      = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>


//================= 	FUNCION PARA LA PAGINACION =======================================//
function pag_Terceros(accion){
	var MyParent = 'bodyTablaBoletas';

	var valor = document.getElementById('inputBuscarGrillaManual').value;
	var filtro = (valor!='')?'AND (retencion LIKE "%'+valor+'%" OR tipo_retencion LIKE "%'+valor+'%" OR valor LIKE "%'+valor+'%" OR base LIKE "%'+valor+'%" OR departamento LIKE "%'+valor+'%" OR ciudad LIKE "%'+valor+'%") ' : '';

	if(accion=='first'){
		var pagina = 1;
		if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
				Ext.get(MyParent).load(
					{
						url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
								modulo            : '<?php echo $modulo; ?>',
								id_documento      : '<?php echo $id_documento; ?>',
								ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
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
						url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
								modulo            : '<?php echo $modulo; ?>',
								id_documento      : '<?php echo $id_documento; ?>',
								ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
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
					url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
								modulo            : '<?php echo $modulo; ?>',
								id_documento      : '<?php echo $id_documento; ?>',
								ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
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
					url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
								modulo            : '<?php echo $modulo; ?>',
								id_documento      : '<?php echo $id_documento; ?>',
								ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
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

	var valor = document.getElementById('inputBuscarGrillaManual').value;
	var filtro = (valor!='')?'AND (retencion LIKE "%'+valor+'%" OR tipo_retencion LIKE "%'+valor+'%" OR valor LIKE "%'+valor+'%" OR base LIKE "%'+valor+'%" OR departamento LIKE "%'+valor+'%" OR ciudad LIKE "%'+valor+'%") ' : '';

	Ext.get(MyParent).load(
						{
							url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
									modulo            : '<?php echo $modulo; ?>',
									id_documento      : '<?php echo $id_documento; ?>',
									ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
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
	var filtro = (valor!='')?'AND (retencion LIKE "%'+valor+'%" OR tipo_retencion LIKE "%'+valor+'%" OR valor LIKE "%'+valor+'%" OR base LIKE "%'+valor+'%" OR departamento LIKE "%'+valor+'%" OR ciudad LIKE "%'+valor+'%") ' : '';
	var MyParent = 'bodyTablaBoletas';
	var limit =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
	var PaginaActual=(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;
	Ext.get(MyParent).load(
						{
							url		: "/LOGICALERP/funciones_globales/grillas/configuracion_retenciones.php",
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
									modulo            : '<?php echo $modulo; ?>',
									id_documento      : '<?php echo $id_documento; ?>',
									ejecutaFuncion    : '<?php echo $ejecutaFuncion; ?>',
									opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
									filtro            : filtro,

								}
						}
					);
}
seleccionaCheck();
function seleccionaCheck() {
	// var arrayTemp=new Array();

	// if('<?php echo $opcGrillaContable ?>'=='FacturaVenta'){arrayTemp=arraytercerosCE;}
	// else{arrayTemp=arrayterceros;}

	//RECORRER EL ARRAY DE LOS CLIENTES, PARA HACER CHECK A LOS QUE YA ESTAN EN LA GRILLA PRINCIPAL DE CONFIGURACION
	for ( i =1; i < arrayRetenciones<?php echo $opcGrillaContable ?>.length ; i ++) {

		if (arrayRetenciones<?php echo $opcGrillaContable ?>[i]!="" && typeof(arrayRetenciones<?php echo $opcGrillaContable ?>[i])!="undefined") {
			if(document.getElementById('checkbox_'+i)){
				document.getElementById('checkbox_'+i).checked=true;
			}
		}
	}

}

function checkGrilla(check,id,retencion,valor,tipo_retencion,base,cuenta) {
	if (check.checked==true) {
		//AGREGAR LA RETENCION EN LA INTERFAZ
		var contenedor=document.getElementById('contenedorCheckbox<?php echo $opcGrillaContable; ?>');
		contenedor.innerHTML=contenedor.innerHTML+'<div class="campoCheck" title="'+retencion+'" id="contenedorRetenciones<?php echo $opcGrillaContable;?>_'+id+'">'
                                        		    +'<div id="cargarCheckbox<?php echo $opcGrillaContable;?>_'+id+'" class="renderCheck"></div>'
                                        		    +'<input type="hidden" class="capturarCheckboxAcumulado<?php echo $opcGrillaContable;?>" id="checkboxRetenciones<?php echo $opcGrillaContable;?>_'+id+'" name="checkbox<?php echo $opcGrillaContable;?>" value="'+valor+'"  />'
                                        		    +'<label class="capturaLabelAcumulado<?php echo $opcGrillaContable;?>" for="checkbox_'+retencion+'">'
                                        		        +'<div class="labelNombreRetencion">'+retencion+'</div>'
                                        		        +'<div class="labelValorRetencion">('+valor+'%)</div>'
                                        		    +'</label>'
                                        		+'</div>';
        // console.log("id: "+id);
		arrayRetenciones<?php echo $opcGrillaContable ?>[id]=id;
       	if (typeof(objectRetenciones_<?php echo $opcGrillaContable; ?>[id])=='undefined') {
       		objectRetenciones_<?php echo $opcGrillaContable ?>[id] = {
                                                                        tipo_retencion : tipo_retencion,
                                                                        base           : base,
                                                                        valor          : valor,
                                                                        cuenta         : cuenta
                                                                      };
       	}
       	else{
       		objectRetenciones_<?php echo $opcGrillaContable; ?>[id].tipo_retencion = tipo_retencion;
       	}


       	//GUARDAR LA RETENCION EN LA BASE DE DATOS
       	<?php echo $ejecutaFuncion; ?>(check);

    }
	else{
		(document.getElementById("contenedorRetenciones<?php echo $opcGrillaContable;?>_"+id)).parentNode.removeChild(document.getElementById("contenedorRetenciones<?php echo $opcGrillaContable;?>_"+id));
		<?php echo $ejecutaFuncion; ?>(check);
		delete arrayRetenciones<?php echo $opcGrillaContable ?>[id]
		delete objectRetenciones_<?php echo $opcGrillaContable; ?>[id];
		// arrayRetenciones<?php echo $opcGrillaContable ?>.remove(id);
	}

}

</script>
<?php

	//BUSQUEDA DE LA GRILLA MANUAL
	function busquedaTerceroPaginacion($id_documento,$modulo,$opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$imprimeVar,$filtro,$link,$id_empresa){

		//SI LA VARIABLE FILTRO NO ESTA VACIA, RECONTAMOS EL LIMITE DE LOS REGISTROS
		if ($filtro!='') {
			$sql="SELECT COUNT(id) as cont  FROM retenciones WHERE activo=1 $filtro AND id_empresa='$id_empresa' AND modulo='$modulo' ";
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
			$sql="SELECT COUNT(id) as cont  FROM retenciones WHERE activo=1 AND id_empresa='$id_empresa' AND modulo='$modulo' ";
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

		$sqlCuentas   = "SELECT id,retencion,tipo_retencion,valor,base,cuenta,departamento,ciudad FROM retenciones WHERE activo=1 $filtro AND id_empresa = '$id_empresa' AND modulo='$modulo'  LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);

		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$rowCuentas['valor'] = $rowCuentas['valor']*1;
			$rowCuentas['base']  = $rowCuentas['base']*1;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo0">'.$contFilaCuenta.'</div>
									<div class="campo1" style="width:300px"  id="retencion_'.$rowCuentas['retencion'].'" title="'.$rowCuentas['retencion'].'">'.$rowCuentas['retencion'].'</div>
									<div class="campo1" style="width:50px" id="valor_'.$rowCuentas['valor'].'">'.$rowCuentas['valor'].'</div>
									<div class="campo1" style="width:70px" id="base_'.$rowCuentas['base'].'">'.$rowCuentas['base'].'</div>
									<div class="campo1" id="departamento_'.$rowCuentas['departamento'].'" title="'.$rowCuentas['departamento'].'">'.$rowCuentas['departamento'].'</div>
									<div class="campo1" id="ciudad_'.$rowCuentas['ciudad'].'" title="'.$rowCuentas['ciudad'].'">'.$rowCuentas['ciudad'].'</div>
									<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas['id'].'" onchange="checkGrilla(this,\''.$rowCuentas['id'].'\',\''.$rowCuentas['retencion'].'\',\''.$rowCuentas['valor'].'\',\''.$rowCuentas['tipo_retencion'].'\',\''.$rowCuentas['base'].'\',\''.$rowCuentas['cuenta'].'\')" value="'.$rowCuentas['id'].'">
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

 ?>