<?php 
include('../../../../configuracion/conectar.php');
include('../../../../configuracion/define_variables.php');

switch ($opc) {
	case 'cuerpoVentanaConfiguracionPos':
		cuerpoVentanaConfiguracionPos();
		break;
}


function cuerpoVentanaConfiguracionPos(){
	
	$date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date); 
    $mes  = date("m", $date); 
    $dia  = date("d",$date);

    //CALCULAR EL FINAL DEL MES
    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

	echo'<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeFacturasCartera">
			<!-- DIV CON LOS TERCEROS A AGREGAR -->
			<div style="width: calc(100% - 250px - 3px); padding:0; float:left; height:375px; margin-left:18px;">
				<div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:20px; margin-top:15px; text-align:center;">FILTRAR POR <br/>CAJAS</div>
				<div style="float:left; margin-left:-33px; margin-top:15px;" class="x-panel-header">
					<img src="img/buscar20.png" onclick="ventanaBusquedaCajas();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar Vendedor" >
				</div>
				<div style="width:100%; height:70%; background-color:#CDDBF0; overflow:hidden;">
					<div id="contenedor_formulario_configuracion" >
						<div id="contenedor_tabla_configuracion" >
							<div class="headTablaBoletas">
								<div class="campo0"></div>
								<div class="campo1">Caja</div>
								<div class="campo4" style="width:25px;">&nbsp;</div>
							</div>
							<div id="bodyTablaConfiguracion" ></div>
						</div>
					</div>
				</div>
			</div>

			<!-- DIV MENU DERECHO -->
			<div style="float:right; width:210px; background-color:#eff3fa; height:100%; border-left:1px solid #8DB2E3;">
				<!-- TIPO DE INFORME -->
				<div style="font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Tipo de Informe</div>
				<div style="margin-left:10px;margin-top:10px; overflow:hidden;">
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_fecha_informe" value="corte" onchange="mostrarOcultarDivFechaInicioCarteraEdades(this.value);" style="float:left; width:30px">
						<div style="float:left;">Con corte a</div>
					</div>
					<div style="margin-bottom:6px; overflow:hidden;">
						<input type="radio" name="tipo_fecha_informe" value="rango_fechas" onchange="mostrarOcultarDivFechaInicioCarteraEdades(this.value);" style="float:left; width:30px">
						<div style="float:left;">Rango de Fechas</div>
					</div>
				</div>

				<!-- FECHAS DE INFORME -->
				<div style="margin:20px 0; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Fecha</div>
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
		</div>
		<script>

		  	new Ext.form.DateField({
				format     : "Y-m-d",
				width      : 100,
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
				width      : 100,
				allowBlank : false,
				showToday  : false,
				applyTo    : "MyInformeFiltroFechaFinal",
				editable   : false,  
				value      : new Date(),                                          
		  	    // listeners  : { select: function() {   } }
		  	});
			
			var elementos = document.getElementsByName("tipo_fecha_informe");
			
			if (localStorage.tipo_fecha_informe_pos=="corte") {
				elementos[0].checked=true;
				document.getElementById("divFechaInicio").style.display="none";
			}
			else if (localStorage.tipo_fecha_informe_pos=="rango_fechas") {
				elementos[1].checked=true;
				document.getElementById("divFechaInicio").style.display="block";
			}else{
				elementos[0].checked=true;
				document.getElementById("divFechaInicio").style.display="none";
			}

			function mostrarOcultarDivFechaInicioCarteraEdades(id){

				if (id=="corte") { document.getElementById("divFechaInicio").style.display="none"; }
				else if (id=="rango_fechas") { document.getElementById("divFechaInicio").style.display="block"; }

			}

			// CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			for ( i = 0; i < arrayConsecutivos.length; i++) {
				if (typeof(arrayConsecutivos[i])!="undefined" && arrayConsecutivos[i]!="") {
					
					//CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            		var div   = document.createElement("div");
            		div.setAttribute("id","fila_consecutivo_caja_"+i);
            		div.setAttribute("class","filaBoleta");
            		document.getElementById("bodyTablaConfiguracion").appendChild(div);

            		//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            		document.getElementById("fila_consecutivo_caja_"+i).innerHTML=consecutivosConfigurados[i];

				}			
			}

		</script>';
}


 ?>