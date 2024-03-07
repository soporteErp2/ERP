<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	$id_empresa = $_SESSION['EMPRESA'];

	switch ($opc) {
		case 'ventanaConfiguracionSituacionFinanciera':
			ventanaConfiguracionSituacionFinanciera();
			break;

		case 'cuerpoVentanaEstadoResultado':
			cuerpoVentanaEstadoResultado($link);
			break;

		case 'buscarCentroCostosNiif':
			buscarCentroCostosNiif($codigo,$id_empresa,$link);
			break;

		case 'cuerpoVentanaConfiguracionBalanceComprobacion':
			cuerpoVentanaConfiguracionBalanceComprobacion();
			break;

		case 'busquedaTerceroPaginacion':
			busquedaTerceroPaginacion($opcGrillaContable,$pagina,$limite,$limit,$rows_registros,$paginas,$id_tercero,$tercero,$nit,$whereSum,$tabla,$estado,$imprimeVar,$filtro,$link,$id_empresa);
			break;
	}

	function ventanaConfiguracionSituacionFinanciera(){

		$date = strtotime(date("Y-m-d"));
	    $anio = date("Y", $date);
	    $mes  = date("m", $date);
	    $dia  = date("d",$date);

	    //CALCULAR EL FINAL DEL MES
	    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

		echo'<div style="width:100%; border-top:1px solid #8DB2E3; padding:25px 0 25px 0; overflow:hidden;">
				<div style="float:left; width:44%; margin-left:18px; border-right:1px solid #8DB2E3;">
					<div style="font-weight: bolder;font-size:12px; margin-bottom:15px; text-align:center;">Tipo de Balance</div>
					<div>
						<input type="radio" name="tipo_balance" value="comprobacion" id="tipo_balance"  onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Balance Clasificado</label>
					</div>
					<div style="margin-top:10px;">
						<input type="radio" name="tipo_balance" value="comparativo" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Balance Comparativo</label>
					</div>
				</div>
				<div style="float:left; width:44%; margin-left:10px; text-align:center;">
					<div style="font-weight: bolder;font-size:12px; margin-bottom:15px;">Nivel de las cuentas</div>
					<div>
						<select id="nivel_cuenta" style="width:100px;">
							<option value="Grupos">Grupo</option>
							<option value="Cuentas">Cuenta</option>
							<option value="Subcuentas">Subcuenta</option>
							<option value="Auxiliares">Auxiliar</option>
						</select>
						<br><br>
						<input type="checkbox" id="mostrar_cuentas" > Mostrar Cuentas
					</div>
				</div>
			</div>

			<div style="float:left;width:100%;margin-bottom:15px;margin-top:2px;text-align:center;font-weight: bolder;font-size:12px;">Fechas del Informe </div>
			<div style="display:table; text-align:center; margin:auto;">
				<div style="display:table-cell;" id="divFechaInicio">
					Fecha Inicial:<br>
					<input type="text" id="MyInformeNiifFechaInicio">
				</div>

				<div style="display: table-cell;">
					Fecha Corte:<br>
					<input type="text" id="MyInformeNiifFechaFinal">
				</div>
			</div>

			<script>
			  	new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 120,
					id         :"cmpFechaInicio",
					allowBlank : false,
					showToday  : false,
					applyTo    : "MyInformeNiifFechaInicio",
					editable   : false,
					value      : "'.$fechaInicial.'"
			  	    // listeners  : { select: function() {   } }
			  	});

				new Ext.form.DateField({
					format     : "Y-m-d",
					width      : 120,
					allowBlank : false,
					showToday  : false,
					applyTo    : "MyInformeNiifFechaFinal",
					editable   : false,
					value      : new Date(),
			  	    // listeners  : { select: function() {   } }
			  	});

				var elementos = document.getElementsByName("tipo_balance");

				//SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES
				//&& localStorage.MyInformeNiifFechaFinal!="" && localStorage.generar!=""
				if (typeof(localStorage.generar_estado_situacion_financiera )!="undefined" &&  localStorage.generar_estado_situacion_financiera !="") {
					document.getElementById("nivel_cuenta").value=localStorage.generar_estado_situacion_financiera;
				}


				if ( typeof(localStorage.tipo_balance_estado_situacion_financiera )!="undefined" &&  localStorage.tipo_balance_estado_situacion_financiera !="") {

					for(var i=0; i<elementos.length; i++) {
						if (elementos[i].value==localStorage.tipo_balance_estado_situacion_financiera ) {tipo_balance=elementos[i].checked=true;}
					}


					document.getElementById("MyInformeNiifFechaFinal").value=localStorage.MyInformeNiifFechaFinal;

					if (localStorage.tipo_balance_estado_situacion_financiera =="comparativo") {
						document.getElementById("MyInformeNiifFechaInicio").value=localStorage.MyInformeNiifFechaInicio;
					}
				}

				if (typeof(localStorage.mostrar_cuentas_estado_situacion_financiera)!="undefined" ) {
					if (localStorage.mostrar_cuentas_estado_situacion_financiera!="") {
						document.getElementById("mostrar_cuentas").checked=localStorage.mostrar_cuentas_estado_situacion_financiera;
					}
				}

				if (localStorage.tipo_balance_estado_situacion_financiera =="comprobacion") {
					document.getElementById("divFechaInicio").style.display="none";
					elementos[0].checked=true;
				}
				else if (localStorage.tipo_balance_estado_situacion_financiera =="comparativo") {
					document.getElementById("divFechaInicio").style.display="block";
					elementos[1].checked=true;
				}else{
					document.getElementById("divFechaInicio").style.display="none";
					elementos[0].checked=true;
				}

				function mostrarOcultarDivFechaInicio(id){

					if (id=="comprobacion") { document.getElementById("divFechaInicio").style.display="none"; }
					else if (id=="comparativo") { document.getElementById("divFechaInicio").style.display="block"; }
					else{ Win_Ventana_configurar_balance_general.close(); }

				}

			</script>';
	}

	//======================== CUERPO DE LA VENTANA DE CONFIGURACION DEL ESTADO DE RESULTADOS ======================//
	function cuerpoVentanaEstadoResultado($link){

		$date = strtotime(date("Y-m-d"));
    	$anio = date("Y", $date);
    	$mes  = date("m", $date);
    	$dia  = date("d",$date);

    	//CALCULAR EL FINAL DEL MES
    	$fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

		echo'<div style="float:left;width:100%; border-top:1px solid #8DB2E3;  overflow:hidden;background-color: #eff3fa;">

				<!-- DIV IZQUIERDO -->
				<div style="width: calc(60% - 1px); float:left; height:100%;">

					<div style="margin-bottom:10px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;border-top:0px;" class="x-panel-header">Centros de Costos</div>

					<!-- MOSTRAR TODOS LOS CENTROS DE COSTOS -->
					<div style="width:100%;height:30px;margin-bottom: 10px;">
						<spam style="cursor:pointer;padding-left: 10px;font-weight:bolder;" onclick="cambiaChekcNiif()"><img src="img/checkox_false.png" id="imgCheckCC">Todos los centros de costos</spam>
					</div>
					<!-- CAMPO DE BUSQUEDA -->
					<div style="height: 28px;padding-top: 5px;padding: 5 0 0 6;background-color: #F3F3F3;border-top-left-radius: 5;border: 1px solid #D4D4D4;float: left;width:140px;margin-left: 8px;border-bottom: none;">
						<input type="text" style="border-radius: 3px;font-size: 12px;min-height: 22px;border: 1px solid #D4D4D4;text-indent: 5px;" id="inputBuscarCentroCostos" onkeyup=" buscarCentroCostosNiif(event,this)" placeholder="codigo c.cost...">
					</div>

					<div style="float: left;width: 35px;height: 34px;background-color: #F3F3F3;border-top-right-radius: 5;border-top: 1px solid #D4D4D4;border-right: 1px solid #d4d4d4;" >
						<img src="img/buscar20.png" onclick="ventanaBuscarCentroCostosNiif()" id="imgBuscarCC" style="padding: 7 0 0 7;cursor: pointer;" title="Buscar Centro de Costos">
					</div>

					<!--CUERPO DE LA GRILLA -->
					<div style="width:100%;float:left;height:100%;">
						<div style="width:95%; height:280px; background-color:#CDDBF0; overflow:hidden;margin-left: 8px;">
							<div id="contenedor_formulario_configuracion">
								<div id="contenedor_tabla_configuracion">
									<div class="headTablaBoletas">
										<div class="campo0"></div>
										<div class="campo1">Codigo</div>
										<div class="campo3" style="width:140px;">Descripcion</div>
										<div class="campo4" style="width:25px;">&nbsp;</div>
									</div>

									<div id="bodyTablaConfiguracion" style="height:238px">
										<div style="width:302px; height:238px; background-color:rgba(214, 211, 211, 0.64); position:absolute; display:none;" id="divDisable"></div>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- DIV DERECHO -->
				<div style="float:left; width:40%; background-color:#eff3fa; height:100%; border-left:1px solid #99BBE8;">

					<!-- NIVEL DE LAS CUENTAS -->
					<div style="width:100%; float:left; height:70px;">
						<div style=" font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none; border-top:0px;" class="x-panel-header">Informe</div>
						<div style="text-align: center;padding-top: 10px;">
							<select id="estado_resultado" style="width:210px;height: 25px;">
								<option value="estado_resultado_integral">Estado de Resultado Integral</option>
								<option value="estado_resultado">Estado de Resultado</option>
								<option value="otro_estado_resultado">Otro estado de Resultado Integral</option>
							</select>
						</div>
					</div>

					<!-- FECHAS -->
					<div style="width: 100%; float:left; height:70px;">
						<div style="font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Fechas del Informe</div>
						<div style="display:table; text-align:center; margin:auto;">
							<div style="display:table-cell;" id="divFechaInicio">
								Fecha Inicial:<br>
								<input type="text" id="MyInformeFiltroFechaInicio" >
							</div>

							<div style="display: table-cell;">
								Fecha Corte:<br>
								<input type="text" id="MyInformeFiltroFechaFinal" >
							</div>
						</div>
					</div>

					<!-- NIVEL DE LAS CUENTAS -->
					<div style="width:100%; float:left; height:100px;">
						<div style=" font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Nivel de las cuentas</div>
						<div style="text-align: center;padding-top: 10px;">
							<select id="nivel_cuenta" style="width:100px;height: 25px;">
									<option value="Grupos">Grupos</option>
									<option value="Cuentas">Cuentas</option>
									<option value="Subcuentas">Subcuentas</option>
									<option value="Auxiliares">Auxiliares</option>
							</select><br><br>
							<input type="checkbox" id="mostrar_cuentas"> Mostrar Cuentas
						</div>
					</div>

					<!-- TIPOS INFORME -->
					<div style="float:left;width:97%;margin-bottom:20px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Tipo de Informe</div>

					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="mensual" id="tipo_balance"  onchange="mostrarOcultarDivFechaInicio(this.value);">
						<label>Mensual</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="mensual_acumulado" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);" >
						<label>Mensual Acumulado</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="comparativo_mensual" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);" >
						<label>Comparativo Mensual</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;display:none;">
						<input type="radio" name="tipo_balance" value="rango_fechas" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);" >
						<label>Rango de Fechas</label>
					</div>
					<div style="margin-top:10px;margin-left:45px;">
						<input type="radio" name="tipo_balance" value="comparativo_anual" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);" >
						<label>Comparativo Anual</label>
					</div>
				</div>

			</div>

			<script>

				function desmarcarSelect(id){
					if (document.getElementById(id).checked==false) {
						arrayCentroCostosNiif.splice(document.getElementById(id).value,1);
						document.getElementById("checkboxSelectAll").checked=false;
						checkBoxSelectAllNiif=false;
					}
					else{
						arrayCentroCostosNiif[document.getElementById(id).value]=document.getElementById(id).value;
					}
				}

				// document.getElementById("checkboxSelectAll").checked=checkBoxSelectAllNiif;
				//VERIFICAR SI ANTERIORMENTE SE HAN SELECCIONADO CENTROS DE COSTOS PARA SELECCIONARLOS DE NUEVO AL ENTRAR EN LA VENTANA DE CONFIGURACION


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

				if (typeof(localStorage.estado_resultado_niif)!="undefined" && localStorage.estado_resultado_niif!="") {
					document.getElementById("estado_resultado").value=localStorage.estado_resultado_niif;
				}
				if (typeof(localStorage.mostrar_cuentas_niif)!="undefined" && localStorage.mostrar_cuentas_niif!="") {
					document.getElementById("mostrar_cuentas").checked=localStorage.mostrar_cuentas_niif;
				}

				if ( typeof(localStorage.nivel_cuentas_EstadoResultadoNiif)!="undefined" &&  localStorage.nivel_cuentas_EstadoResultadoNiif!="") {
					document.getElementById("nivel_cuenta").value=localStorage.nivel_cuentas_EstadoResultadoNiif;
				}

				var elementos = document.getElementsByName("tipo_balance");

				//SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES
				if ( typeof(localStorage.tipo_balance_EstadoResultadoNiif)!="undefined" &&  localStorage.tipo_balance_EstadoResultadoNiif!="") {

					for(var i=0; i<elementos.length; i++) {
						if (elementos[i].value==localStorage.tipo_balance_EstadoResultadoNiif) {elementos[i].checked=true;}
					}

					// document.getElementById("nivel_cuenta").value=localStorage.generar;
					document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalEstadoResultadoNiif;

					if (localStorage.tipo_balance_EstadoResultadoNiif=="rango_fechas") {
						document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioEstadoResultadoNiif;
					}

				}
				else{
					elementos[0].checked=true;
				}

				if (localStorage.tipo_balance_EstadoResultadoNiif!="rango_fechas") {
					document.getElementById("divFechaInicio").style.display="none";
				}
				else if (localStorage.tipo_balance_EstadoResultadoNiif=="rango_fechas") {
					document.getElementById("divFechaInicio").style.display="block";
				}else{
					document.getElementById("divFechaInicio").style.display="none";
				}

				function mostrarOcultarDivFechaInicio(id){

					if (id!="rango_fechas") { document.getElementById("divFechaInicio").style.display="none"; }
					else if (id=="rango_fechas") { document.getElementById("divFechaInicio").style.display="block"; }

				}

				if ( typeof(localStorage.sucursales_estado_resultado_niif)!="undefined" &&  localStorage.sucursales_estado_resultado_niif!="") {
					setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_estado_resultado").value=localStorage.sucursales_estado_resultado_niif;},100);
				}

				//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
				for ( i = 0; i < arrayCentroCostosNiif.length; i++) {
					if (typeof(arrayCentroCostosNiif[i])!="undefined" && arrayCentroCostosNiif[i]!="") {

						//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
    	        		var div   = document.createElement("div");
    	        		div.setAttribute("id","fila_centro_costos_"+i);
    	        		div.setAttribute("class","filaBoleta");
    	        		document.getElementById("bodyTablaConfiguracion").appendChild(div);

    	        		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
    	        		document.getElementById("fila_centro_costos_"+i).innerHTML=centroCostosConfiguradosNiif[i];

					}
				}

				if (checkBoxSelectAllNiif=="true") {
					document.getElementById("imgCheckCC").setAttribute("src","img/checkox_true.png");
					document.getElementById("inputBuscarCentroCostos").disabled=true;
					document.getElementById("divDisable").style.display="block";
					document.getElementById("imgBuscarCC").style.display="none";
				}

				//FUNCION DEL CHECKBOX
				function cambiaChekcNiif(){
					var elemento = document.getElementById("imgCheckCC").getAttribute("src");

					if (elemento=="img/checkox_false.png") {
						document.getElementById("imgCheckCC").setAttribute("src","img/checkox_true.png");
						document.getElementById("inputBuscarCentroCostos").disabled=true;
						document.getElementById("divDisable").style.display="block";
						document.getElementById("imgBuscarCC").style.display="none";
						checkBoxSelectAllNiif="true";
					}
					else{
						document.getElementById("imgCheckCC").setAttribute("src","img/checkox_false.png");
						document.getElementById("inputBuscarCentroCostos").disabled=false;
						document.getElementById("divDisable").style.display="none";
						document.getElementById("imgBuscarCC").style.display="block";
						checkBoxSelectAllNiif="false";
					}

				}

			</script>';
	}

	//======================= FUNCION PARA BUSCAR EL CENTRO DE COSTOS POR CODIGO =================================//
	function buscarCentroCostosNiif($codigo,$id_empresa,$link){
		$sql="SELECT id,nombre FROM centro_costos WHERE activo=1 AND id_empresa=$id_empresa AND codigo='$codigo' ";
		$query  = mysql_query($sql,$link);
		$id     = mysql_result($query,0,'id');
		$nombre = mysql_result($query,0,'nombre');

		if ($id!="" && $nombre!=""){
			echo json_encode(array('id' => $id, 'nombre' => $nombre));
		}
		else{
			echo "false";
		}
	}

	//========================== CUERPO DE LA VENTANA DE CONFIGURACION DEL BALANCE DE PRUEBA =======================//

	function cuerpoVentanaConfiguracionBalanceComprobacion(){

		$date = strtotime(date("Y-m-d"));
	    $anio = date("Y", $date);
	    $mes  = date("m", $date);
	    $dia  = date("d",$date);

	    //CALCULAR EL FINAL DEL MES
	    $fechaInicial = date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

		echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
				<!-- DIV CON LOS TERCEROS A AGREGAR -->
				<div style="width: calc(100% - 250px - 3px); padding:0; float:left; height:240px; margin-left:18px;">
					<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR TERCERO</div>

					<!-- MOSTRAR TODOS LOS TERCEROS-->
					<div style="width:100%;height:30px;">
						<spam style="cursor:pointer;padding-left: 10px;font-weight:bolder;float:left;margin-bottom: 7px;" onclick="cambiaChekc()"><img src="img/checkox_false.png" id="imgCheckTercerosBC">Todos los Terceros</spam><br>
						<!--<spam style="float:right;font-weight: bolder;font-size:13px;" >FILTRO POR TERCERO</spam>-->
					</div>

					<!-- CAMPO BUSCAR TERCERO -->
					<div style="display:none;height: 28px;padding-top: 5px;padding: 5 0 0 6;background-color: #F3F3F3;border-top-left-radius: 5;border: 1px solid #D4D4D4;float: left;width: 140px;border-bottom: 0px;">
						<!--<input type="text" style="border-radius: 3px;font-size: 12px;min-height: 22px;border: 1px solid #D4D4D4;text-indent: 5px;" id="inputBuscarCentroCostos" onkeyup=" buscarterceroBC(event,this)" placeholder="nit tercero...">-->
					</div>

					<!-- VENTANA BUSCAR TERCERO -->
					<div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
						<img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
					</div>

					<div style="width:100%;height:90%;background-color: #CDDBF0;overflow:hidden;">
						<div id="contenedor_formulario_configuracion" >
							<div id="contenedor_tabla_configuracion" >
								<div class="headTablaBoletas">
									<div class="campo0">&nbsp;</div>
									<div class="campo1">Nit</div>
									<div class="campo2">Tercero</div>
									<div class="campo4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
								</div>
								<div id="bodyTablaConfiguracion">
									<div style="width: 481px; height: 164px; position: absolute; display: none; background-color: rgba(214, 211, 211, 0.639216);" id="divDisableBC"></div>
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

					<!-- RANGO DE LAS CUENTAS -->
					<div style="margin:25px 0; font-weight: bolder; font-size:12px; border-right:0px; border-left:none; text-align:center;" class="x-panel-header">Rango de Cuentas</div>
					<div style="margin:0 7px; float:left;">
						<div style="float:left;">
							<div style="float:left; width:70px; margin-right:5px">Cuenta Inicial:</div>
							<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_inicial" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
						</div>
						<div style="float:left; margin-top: 2px; margin-left:-20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_inicial\')" >
							<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
						</div>

						<div style="float:left; margin-top:20px;">
							<div style="float:left; width:70px; margin-right:5px">Cuenta Final:</div>
							<div style="float:left; width:120px;"><input type="text" class="myField" id="cuenta_final" style="width:120px" onkeyup="validaCuentaPuc(event,this);"></div>
						</div>
						<div style="float:left; margin-top: 22px; margin-left: -20px; background-image: url(\'img/MyGrillaFondo.png\');" onclick="ventanaBuscarCuentaPuc(\'cuenta_final\')" >
							<img src="img/buscar20.png" style="width:18px;height:18px;cursor:pointer;" title="Buscar cuenta en el Puc">
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


				document.getElementById("cuenta_inicial").value=(typeof(localStorage.cuenta_inicialBCNiif )!="undefined")? localStorage.cuenta_inicialBCNiif  : "" ;
				document.getElementById("cuenta_final").value=(typeof(localStorage.cuenta_finalBCNiif )!="undefined")? localStorage.cuenta_finalBCNiif  : "" ;

				if (typeof(localStorage.sucursal_balance_comprobacionNiif)!="undefined") {
					if (localStorage.sucursal_balance_comprobacionNiif!="") {
						setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_balance_comprobacion").value=localStorage.sucursal_balance_comprobacionNiif;},100);
					}
				}

				if (typeof(localStorage.MyInformeFiltroFechaInicioBalanceComprobacionNiif)!="undefined") {
					if (localStorage.MyInformeFiltroFechaInicioBalanceComprobacionNiif!="") {
						document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioBalanceComprobacionNiif;
					}
				}

				if (typeof(localStorage.MyInformeFiltroFechaFinalBalanceComprobacionNiif)!="undefined") {
					if (localStorage.MyInformeFiltroFechaFinalBalanceComprobacionNiif!="") {
						document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalBalanceComprobacionNiif;
					}
				}

				// if (localStorage.tipo_fecha_informe=="corte") {
				// 	document.getElementById("divFechaInicio").style.display="none";
				// 	elementos[0].checked=true;
				// }
				// else if (localStorage.tipo_fecha_informe=="rango_fechas") {
				// 	document.getElementById("divFechaInicio").style.display="block";
				// 	elementos[1].checked=true;
				// }else{
				// 	document.getElementById("divFechaInicio").style.display="none";
				// 	elementos[0].checked=true;
				// }

				//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
				for ( i = 0; i < arraytercerosBCNiif.length; i++) {
					if (typeof(arraytercerosBCNiif[i])!="undefined" && arraytercerosBCNiif[i]!="") {

						//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
	            		var div   = document.createElement("div");
	            		div.setAttribute("id","fila_cartera_cliente_"+i);
	            		div.setAttribute("class","filaBoleta");
	            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

	            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
	            		document.getElementById("fila_cartera_cliente_"+i).innerHTML=tercerosConfiguradosBCNiif[i];

					}
				}

				if (checkBoxSelectAllTercerosBCNiif=="true") {
					document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_true.png");
					// document.getElementById("inputBuscarCentroCostos").disabled=true;
					document.getElementById("divDisableBC").style.display="block";
					document.getElementById("imgBuscarTerceroBC").style.display="none";
				}


				//FUNCION DEL CHECKBOX
				function cambiaChekc(){
					var elemento = document.getElementById("imgCheckTercerosBC").getAttribute("src");

					if (elemento=="img/checkox_false.png") {
						document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_true.png");
						// document.getElementById("inputBuscarCentroCostos").disabled=true;
						document.getElementById("divDisableBC").style.display="block";
						document.getElementById("imgBuscarTerceroBC").style.display="none";
						checkBoxSelectAllTercerosBCNiif="true";
					}
					else{
						document.getElementById("imgCheckTercerosBC").setAttribute("src","img/checkox_false.png");
						// document.getElementById("inputBuscarCentroCostos").disabled=false;
						document.getElementById("divDisableBC").style.display="none";
						document.getElementById("imgBuscarTerceroBC").style.display="block";
						checkBoxSelectAllTercerosBCNiif="false";
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

		$sqlCuentas   = "SELECT $id_tercero,$tercero,$nit $whereSum FROM $tabla WHERE activo=1 $estado $filtro AND id_empresa='$id_empresa' GROUP BY $id_tercero ASC LIMIT $limit";
		$queryCuentas = mysql_query($sqlCuentas,$link);
		while ($rowCuentas = mysql_fetch_array($queryCuentas)) {
			$contFilaCuenta++;

			$divSaldoPendiente=($tabla!='terceros')? '<div class="campo3" id="saldo_'.$contFilaCuenta.'">'.$rowCuentas['saldo'].'</div>' : '' ;

			$filaInsertBoleta .= '<div class="filaBoleta" id="fila_boleta_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
									<div class="campo0">'.$contFilaCuenta.'</div>
									<div class="campo1" id="nit_'.$rowCuentas[$id_tercero].'">'.$rowCuentas['nit'].'</div>
									<div class="campo2" style="border-left:0px;" id="tercero_'.$rowCuentas[$id_tercero].'" title="'.$rowCuentas[$tercero].'">'.$rowCuentas[$tercero].'</div>
									'.$divSaldoPendiente.'
									<div class="campo4" id="valor_anticipo_'.$opcGrillaContable.'_'.$contFilaCuenta.'">
										<input type="checkbox" id="checkbox_'.$rowCuentas[$id_tercero].'" onchange="checkGrilla(this,\''.$rowCuentas[$id_tercero].'\')" value="'.$rowCuentas[$id_tercero].'" >
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

							</script>';

			echo $filaInsertBoleta;

	}

?>