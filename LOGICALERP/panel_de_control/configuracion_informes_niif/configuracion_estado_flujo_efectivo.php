<?php

	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa        = $_SESSION['EMPRESA'];
	$opcGrillaContable = 'configuracionFlujoEfectivo';

	//CONSULTAR EL PUC NIIF
	$limit = '100';
	$sql   = "SELECT COUNT(pn.id) as cont FROM puc_niif AS pn
				WHERE pn.activo=1
					AND pn.id_empresa=$id_empresa
					AND LENGTH(CAST(pn.cuenta AS CHAR))=2
					AND pn.id NOT IN (SELECT id_cuenta AS id
									FROM configuracion_informe_estado_flujo_efectivo
									WHERE id_cuenta=pn.id  AND id_empresa=$id_empresa AND contabilidad='niif')";
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

	$sqlCuentas   = "SELECT pn.id,cuenta,pn.descripcion
					FROM puc_niif AS pn
					WHERE pn.activo=1 AND pn.id_empresa=$id_empresa AND LENGTH(CAST(pn.cuenta AS CHAR))=2
					AND pn.id NOT IN (SELECT id_cuenta AS id
									FROM configuracion_informe_estado_flujo_efectivo
									WHERE id_cuenta=pn.id  AND id_empresa=$id_empresa AND contabilidad='niif')
					ORDER BY CAST(pn.cuenta AS CHAR) ASC LIMIT $limit";
	$queryCuentas = mysql_query($sqlCuentas,$link);

	while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
		$contFilaCuenta++;
		$filaInsertBoleta .= '<div class="filaBoleta" id="fila_grilla_'.$opcGrillaContable.'_'.$rowCuentas['id'].'" draggable="true" ondragstart="drag'.$opcGrillaContable.'(event,this)">
								<div class="campo0" id="contFila">'.$contFilaCuenta.'</div>
								<div class="campo4" id="cuenta_'.$rowCuentas['id'].'">'.$rowCuentas['cuenta'].'</div>
								<div class="campo2" id="descripcion_'.$rowCuentas['id'].'" title="'.$rowCuentas['descripcion'].'">'.$rowCuentas['descripcion'].'</div>
								<div style="float: left;width: 20px;height: 20px;overflow: hidden;" id="load_fila_'.$rowCuentas['id'].'"></div>
							</div>';
	}

	$contFilaCuenta =  0;
	$filasingresos  = '';
	$filascostos    = '';
	$filasimpuestos = '';
	$filasgastos    = '';

	//CONSULTAR LAS CUENTAS YA CONFIGURADAS PARA MOSTRARLAS
	$sql   = "SELECT id_cuenta,cuenta,descripcion,clasificacion FROM configuracion_informe_estado_flujo_efectivo WHERE  activo=1 AND contabilidad='niif' AND id_empresa=$id_empresa  ";
	$query = mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		$contFilaCuenta++;
		$filas_configuracion = '<div class="filaBoleta" id="fila_grilla_'.$opcGrillaContable.'_'.$row['id_cuenta'].'" draggable="true" ondragstart="drag'.$opcGrillaContable.'(event,this)">
									<div class="campo0" id="contFila">'.$contFilaCuenta.'</div>
									<div class="campo4" id="cuenta_'.$row['id_cuenta'].'">'.$row['cuenta'].'</div>
									<div class="campo2" id="descripcion_'.$row['id_cuenta'].'" title="'.$row['descripcion'].'">'.$row['descripcion'].'</div>
									<div style="float: left;width: 20px;height: 20px;overflow: hidden;" id="load_fila_'.$row['id_cuenta'].'"></div>
								</div>';

		if ($row['clasificacion']=='actividades_operacionales') { $filas_actividades_operacionales.=$filas_configuracion; }
		else if ($row['clasificacion']=='actividades_inversiones') { $filas_actividades_inversiones.=$filas_configuracion; }
		else if ($row['clasificacion']=='actividades_financieras') { $filas_actividades_financieras.=$filas_configuracion; }
	}

 ?>
<style>
	.contenedorPrincipal{
		width            : 100%;
		float            : left;
		background-color : #DFE8F6;
		height: 100% !important;
	}
	.titulosVentanaConfiguracionPos{
		text-align : center;
		float       : left;
		color       : #15428b;
		font-weight : bold;
		font-size   : 11px;
		font-family : tahoma,arial,verdana,sans-serif;
		width       : 50%;
		margin-top  : 15px;
	}

	.titulosVentanaConfiguracionPos > div{
		text-align  : center;
		font-weight : bold;
		color       : #000;
		font-size   : 11px;
		width       : 70%;
		margin      : 0 auto;
	}

	.contenedor_configuracion_informe{
		width            : 47%;
		height           : calc(100% - 130px);
		margin           : 1%;
		float            : left;
		overflow         : hidden;
	}
	.contenedor_configuracion_informe > div{
		overflow-x       : hidden;
		overflow-y       : auto;
		width            : 99%;
		height           : calc((100% - 130px)/4);
		background-color : #FFF;
		border           : 1px solid #d4d4d4;
	}

	/* FILAS  DE LAS CUENTAS AGREGADAS, FUERA DE LA GRILLA */
	.contenedor_configuracion_informe > div >div{
		border-bottom : 1px solid #d4d4d4;
		height        : 21px !important;
	}


	/*	CAMPOS DE LAS FILAS DE LAS CUENTAS AGREGADAS */
	.contenedor_configuracion_informe > div >div >div{ height : 100% !important; }
	.contenedor_configuracion_informe > div .campo0{ display : none; }
	.contenedor_configuracion_informe > div .campo2{ width : 270px; }
	.contenedor_configuracion_informe > div .campo4{ width : 50px; }
	.contenedor_configuracion_informe > div >div{ height : 18px; }

	.titulos_contenedor_configuracion{
		background-color : #F3F3F3 !important;
		border-bottom    : 0px !important;
		font-weight      : bold;
		padding-top      : 5;
		font-size        : 11px;
		text-indent      : 10px;
		height       : 20px !important;
		margin-bottom    : 0px !important;
	}

	.divLoading{
		position     : absolute;
		width        : 100%;
		height       : 95%;
		color        : #FFF;
		font-weight  : bold;
		display      : none;
	}

	#configFlujoEfectivo .campo2{
		width        : calc(100% - 120px);
		border-right : none;
	}

	#configFlujoEfectivo .over {
	  border: 2px dashed #000 !important;
	}

</style>

<!-- <div id="barraBotonesConfigPos" style="border-bottom:1px solid #99BBE8;"></div> -->
<div class="divLoading" id="divContenedorLoadingConfiguracion"><div id="experiment">
        <div style="display:none;" id="divLoadConfiguracion"></div>
    </div>
</div>


<div class="contenedorPrincipal" id="configFlujoEfectivo">
	<div class="titulosVentanaConfiguracionPos" style="width:100%; ">
		<div>Arrastre y suelte las cuentas en cada clasificaci&oacute;n que correspondan.</div>
	</div>
	<div class="titulosVentanaConfiguracionPos">CUENTAS NIIF</div>
	<div class="titulosVentanaConfiguracionPos">CONFIGURACION DEL INFORME</div>

	<div id="contenedor_grilla_manual" style="float:left; height: calc(100% - 140px); width:47%;">
		<div class="toolbar_grilla_manual" >
			<div class="div_input_busqueda_grilla_manual">
				<input type="text" id="inputBuscarGrillaManual<?php echo $opcGrillaContable; ?>" onkeyup="inputBuscarGrillaManual<?php echo $opcGrillaContable; ?>(event,this);">
			</div>
			<div class="div_img_actualizar_datos_grilla_manual">
				<img src="img/reload_grilla.png" onclick="actualizarDatosGrillaManual<?php echo $opcGrillaContable; ?>();">
			</div>
		</div>
		<div id="contenedor_tabla_boletas" style="height: calc(100% - 36px);">
			<div class="headTablaBoletas">
				<div class="campo0">&nbsp;</div>
				<div class="campo4">Cuenta</div>
				<div class="campo2" style="border-left:0px;">Descripcion</div>
			</div>
			<div id="bodyTablaBoletasFlujoEfectivo" style="height: calc(100% - 55px);" ondrop="drop_grilla_manual<?php echo $opcGrillaContable; ?>(event)" ondragover="allowDrop<?php echo $opcGrillaContable; ?>(event)"><?php echo $filaInsertBoleta; ?></div>
			<div style="float:right; margin:2 20px 0 0;">
				<div style="float:left; margin:2px 5px 0 5px;font-weight:bold;" id="labelPaginacion">Pagina 1 de <?php echo $paginas; ?></div>
				<div class="my_first" onclick="pag_grilla<?php echo $opcGrillaContable; ?>('first')"></div>
				<div class="my_prev" onclick="pag_grilla<?php echo $opcGrillaContable; ?>('prev')"></div>
				<div class="my_next" onclick="pag_grilla<?php echo $opcGrillaContable; ?>('next')"></div>
				<div class="my_last" onclick="pag_grilla<?php echo $opcGrillaContable; ?>('last')"></div>
			  </div>
		</div>
	</div>
	<div class="contenedor_configuracion_informe" id="divPadreConfiguracion">
		<div class="titulos_contenedor_configuracion">ACTIVIDADES OPERACIONALES</div>
		<div  id="actividades_operacionales" ondrop="drop<?php echo $opcGrillaContable; ?>(event,this); handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)" ondragover="allowDrop<?php echo $opcGrillaContable; ?>(event); handleDragOver_<?php echo $opcGrillaContable; ?>(event,this)" ondragleave="handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)">
			<?php echo $filas_actividades_operacionales; ?>
		</div>
		<div class="titulos_contenedor_configuracion">ACTIVIDADES DE INVERSIONES</div>
		<div  id="actividades_inversiones" ondrop="drop<?php echo $opcGrillaContable; ?>(event,this); handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)" ondragover="allowDrop<?php echo $opcGrillaContable; ?>(event); handleDragOver_<?php echo $opcGrillaContable; ?>(event,this)" ondragleave="handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)">
			<?php echo $filas_actividades_inversiones; ?>
		</div>
		<div class="titulos_contenedor_configuracion">ACTIVIDADES FINANCIERAS</div>
		<div  id="actividades_financieras" ondrop="drop<?php echo $opcGrillaContable; ?>(event,this); handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)" ondragover="allowDrop<?php echo $opcGrillaContable; ?>(event); handleDragOver_<?php echo $opcGrillaContable; ?>(event,this)" ondragleave="handleDragLeave_<?php echo $opcGrillaContable; ?>(event,this)">
			<?php echo $filas_actividades_financieras; ?>
		</div>
	</div>
</div>

<script>
	function handleDragOver_<?php echo $opcGrillaContable; ?>(e,div) { div.classList.add('over'); } 	  // this / e.target is the current hover target.
	function handleDragLeave_<?php echo $opcGrillaContable; ?>(e,div) { div.classList.remove('over'); }   // this / e.target is previous target element.


	//VARIABLE GLOBAL PARA IDENTIFICAR DE DONDE SE ESTA SACANDO EL DIV
	var fromElement='';

	function allowDrop<?php echo $opcGrillaContable; ?>(ev){
		ev.preventDefault();
	}

	function drag<?php echo $opcGrillaContable; ?>(ev,elemento){
		ev.dataTransfer.setData("Text",ev.target.id);
		fromElement=elemento.parentNode.id;
	}

	function drop<?php echo $opcGrillaContable; ?>(ev,div){
		ev.preventDefault();
		var data=ev.dataTransfer.getData("Text");
		var id_cuenta = data.split("_")[3];
		var elementInicio = '';

		var divMoverPadre=ev.target.parentNode.parentNode.id;
		if (div.id == fromElement) { return; }

		if (divMoverPadre=='divPadreConfiguracion') {
			elementInicio = ev.target.parentNode.id;
			ev.target.parentNode.appendChild(document.getElementById(data));
		}
		else if(ev.target.id=='actividades_operacionales' || ev.target.id=='actividades_inversiones' || ev.target.id=='actividades_financieras'){
			elementInicio = ev.target.id;
			ev.target.appendChild(document.getElementById(data));
		}
		else {
			elementInicio = ev.target.parentNode.parentNode.id;
			ev.target.parentNode.parentNode.appendChild(document.getElementById(data));
		}

		ajaxConfiguracion<?php echo $opcGrillaContable; ?>(fromElement,elementInicio,id_cuenta);
		//LIMPIAR EL ELEMENTO DE INICIO
		fromElement='';
	}

	function drop_grilla_manual<?php echo $opcGrillaContable; ?>(ev) {
		ev.preventDefault();
		var data=ev.dataTransfer.getData("Text");
	    var id_cuenta = data.split("_")[3];
	    var campo = ev.target.id.split("_")[0];

	    if (fromElement=='bodyTablaBoletasFlujoEfectivo') { return; }

	    if(campo=='fila'){
	    	ev.target.parentNode.appendChild(document.getElementById(data));
	    }
	    else if (ev.target.id!='bodyTablaBoletasFlujoEfectivo') {
	    	ev.target.parentNode.parentNode.appendChild(document.getElementById(data));
	    }
	    else {

	    	ev.target.appendChild(document.getElementById(data));
	    }

		ajaxConfiguracion<?php echo $opcGrillaContable; ?>(fromElement,'bodyTablaBoletasFlujoEfectivo',id_cuenta);
		//LIMPIAR EL ELEMENTO DE INICIO
		fromElement='';
	}

	//FUNCION PARA INSERTAR, ELIMINAR O ACTUALIZAR LA CONFIGURACION DE LOS REGISTROS
	function ajaxConfiguracion<?php echo $opcGrillaContable; ?>(elementInicio,elementFin,id_cuenta){
		if (elementInicio==elementFin) {return;}

		if (elementInicio=='bodyTablaBoletasFlujoEfectivo') { opc_registro='insert'; }
		else if (elementFin=='bodyTablaBoletasFlujoEfectivo') { opc_registro='delete'; }
		else {opc_registro='update'; }

		// var opc_registro=(elementInicio=='bodyTablaBoletasFlujoEfectivo')? 'insert': 'update' ;
		document.getElementById('divContenedorLoadingConfiguracion').style.display='block';
		Ext.get('load_fila_'+id_cuenta).load({
			url     : 'configuracion_informes_niif/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				opc           : 'configurar_cuenta_flujo_efectivo',
				opc_registro  : opc_registro,
				elementInicio : elementInicio,
				clasificacion : elementFin,
				informe       : 'estado_de_resultado',
				id_cuenta   : id_cuenta,
			}
		});
	}

	//================================ MANEJO DE LA GRILLA ==================================//

	//VARIABLES PARA LA PAGINACION
	arrayLimitGrilla<?php echo $opcGrillaContable; ?> = new Array();
	PaginaActual<?php echo $opcGrillaContable; ?>     = 1;
	MaxPage<?php echo $opcGrillaContable; ?>          = <?php echo $paginas; ?>;
	<?php echo $acumScript; ?>

	function inputBuscarGrillaManual<?php echo $opcGrillaContable; ?>(event,input) {
		 var tecla   = input ? event.keyCode : event.which
	    ,   valor  = input.value;
	    if (tecla==13) {
	    	buscarDatosGrillaManual<?php echo $opcGrillaContable; ?>(valor);
	    }
	}

	//=========================== FUNCION PARA BUSCAR REGISTROS POR UN VALOR =========================================//
	function buscarDatosGrillaManual<?php echo $opcGrillaContable; ?>(valor) {
		var MyParent = 'bodyTablaBoletasFlujoEfectivo';
		// var valor = document.getElementById('inputBuscarGrillaManual').value;
		var filtro = (valor!='')?'AND (pn.cuenta LIKE "%'+valor+'%" OR  pn.descripcion LIKE "%'+valor+'%")' : '';

		var limit =(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")? arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>] : '0,<?php echo $limit ?>';
		var PaginaActual=(typeof(arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>])!="undefined")?PaginaActual<?php echo $opcGrillaContable; ?> : '1' ;
		Ext.get(MyParent).load(
							{
								url		: "configuracion_informes_niif/bd/bd.php",
								scripts	: true,
								nocache	: true,
								params	:
									{
										opc               : 'busquedaTerceroPaginacion',
										limite            : '<?php echo $limit; ?>',
										limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
										rows_registros    : '<?php echo $rows_registros; ?>',
										paginas           : '<?php echo $paginas; ?>',
										tabla             : '<?php echo puc_niif; ?>',
										pagina            : PaginaActual<?php echo $opcGrillaContable; ?>,
										imprimeVar        : '',
										opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
										filtro            : filtro,

									}
							}
						);
	}

	//FUNCION  PARA ACTUALIZAR LA GRILLA
	function actualizarDatosGrillaManual<?php echo $opcGrillaContable; ?>(){
		var MyParent = 'bodyTablaBoletasFlujoEfectivo';
		var valor = document.getElementById('inputBuscarGrillaManual<?php echo $opcGrillaContable; ?>').value;
		var filtro = (valor!='')?'AND (pn.cuenta LIKE "%'+valor+'%" OR  pn.descripcion LIKE "%'+valor+'%")' : '';


		Ext.get(MyParent).load(
							{
								url		: "configuracion_informes_niif/bd/bd.php",
								scripts	: true,
								nocache	: true,
								params	:
									{
										opc               : 'busquedaTerceroPaginacion',
										limite            : '<?php echo $limit; ?>',
										limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
										rows_registros    : '<?php echo $rows_registros; ?>',
										paginas           : '<?php echo $paginas;    ?>',
										tabla             : '<?php echo puc_niif;     ?>',
										pagina            : PaginaActual<?php echo $opcGrillaContable; ?>,
										imprimeVar        : '',
										opcGrillaContable :'<?php echo $opcGrillaContable; ?>',
										filtro            : filtro,

									}
							}
						);
	}

	//================= 	FUNCION PARA LA PAGINACION =======================================//
	function pag_grilla<?php echo $opcGrillaContable; ?>(accion){
		var MyParent = 'bodyTablaBoletasFlujoEfectivo';
		var valor = document.getElementById('inputBuscarGrillaManual').value;
		var filtro = (valor!='')?'AND (pn.cuenta LIKE "%'+valor+'%" OR  pn.descripcion LIKE "%'+valor+'%")' : '';

		if(accion=='first'){
			var pagina = 1;
			if(PaginaActual<?php echo $opcGrillaContable; ?>!=1){
					Ext.get(MyParent).load(
						{
							url		: "configuracion_informes_niif/bd/bd.php",
							scripts	: true,
							nocache	: true,
							params	:
								{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									tabla             : '<?php echo puc_niif;     ?>',
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
							url		: "configuracion_informes_niif/bd/bd.php",
							scripts	: true,
							nocache	: true,
							params	:
								{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									tabla             : '<?php echo puc_niif;     ?>',
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
						url		: "configuracion_informes_niif/bd/bd.php",
						scripts	: true,
						nocache	: true,
						params	:
							{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									tabla             : '<?php echo puc_niif;     ?>',
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
						url		: "configuracion_informes_niif/bd/bd.php",
						scripts	: true,
						nocache	: true,
						params	:
							{
									opc               : 'busquedaTerceroPaginacion',
									limite            : '<?php echo $limit; ?>',
									limit             : arrayLimitGrilla<?php echo $opcGrillaContable; ?>[PaginaActual<?php echo $opcGrillaContable; ?>],
									rows_registros    : '<?php echo $rows_registros; ?>',
									paginas           : '<?php echo $paginas;    ?>',
									tabla             : '<?php echo puc_niif;     ?>',
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
</script>