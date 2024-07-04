<?php
    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');

    $id_empresa = $_SESSION['EMPRESA'];

    $sql="SELECT id,descripcion FROM nomina_tipo_contrato WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $tipo_contrato.="<option value='$row[id]'>$row[descripcion]</option>";
    }

?>
<div style="border-top:1px solid #8DB2E3; width:100%; overflow:hidden;" id="informeBalancePrueba">
    <!-- DIV MENU IZQUIERDO -->
    <div style="width: calc(100% - 215px - 3px); padding:0; float:left; height:270px; margin-left:5px;float:left;">

        <div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR EMPLEADO(S)</div>

        <!-- VENTANA BUSCAR TERCERO -->
        <div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
            <img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
        </div>

        <div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
            <div id="contenedor_formulario_configuracion" >
                <div id="contenedor_tabla_configuracion" style="height:178px;">
                    <div class="headTablaBoletas">
                        <div class="campoInforme0">&nbsp;</div>
                        <div class="campoInforme1">Documento</div>
                        <div class="campoInforme2" style="width: 150px;">Empleado</div>
                        <div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas('empleados');" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
                    </div>
                    <div id="bodyTablaConfiguracion" style="height:140px;">

                    </div>

                </div>
            </div>
        </div>

        <div style="float:left; width:100%; font-weight:bolder; font-size:12px; margin-bottom:10px; margin-top:10px; text-align:center;">FILTRAR POR CONCEPTO(S)</div>

        <!-- VENTANA BUSCAR TERCERO -->
        <div style="display:none;background-color: #F3F3F3;border-right: 1px solid #D4D4D4;border-top: 1px solid #D4D4D4;float: left;height: 26px;width: 35px;border-top-right-radius: 5px;padding: 7 0 0 7;" >
            <img src="img/buscar20.png" onclick="ventanaBusquedaTercero();" style="cursor: pointer;width:21px;height:21px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC5">
        </div>

        <div style="width:100%;height:180px;background-color: #CDDBF0;overflow:hidden;">
            <div id="contenedor_formulario_configuracion" >
                <div id="contenedor_tabla_configuracion" style="height:178px;">
                    <div class="headTablaBoletas">
                        <div class="campoInforme0">&nbsp;</div>
                        <div class="campoInforme1" style="width:220px;">Concepto</div>
                        <div class="campoInforme2" style="width: 30px;" title="Naturaleza del Concepto">Nat.</div>
                        <div class="campoInforme4" style="width:25px;"><img src="img/buscar20.png" onclick="ventanaBusquedaGrillas();" style="cursor: pointer;width:16px;height:16px;margin-top:0px;" title="Buscar tercero" id="imgBuscarTerceroBC"></div>
                    </div>
                    <div id="bodyTablaConfiguracionVendedores" style="height:140px;">

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

        <div style="margin-bottom:25px; margin-top:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Tipo de contrato</div>
        <div style="display:table; margin:auto;">
            <div style="overflow:hidden;" id="divFechaInicio">
                <div style="margin-bottom:15px; text-align:center;">
                    <select type="text" id="tipo_contrato" style="width:120px">
                        <option value="todos">Todos</option>
                        <?php echo $tipo_contrato ?>
                    </select>
                </div>
            </div>
        </div>

        <div style="margin-bottom:25px; margin-top:25px; font-weight: bolder; font-size:12px; text-align:center; border-right:0px; border-left:none;" class="x-panel-header">Agrupado por</div>
        <div style="float:left; width:100%; border-right:1px solid #8DB2E3;">
            <div style="margin-left:18px;">
                <input type="radio" name="agrupado" value="empleados" id="agrupado_empleados"  onchange="">
                <label>Empleados</label>
            </div>
            <div style="margin-top:10px;margin-left:18px;">
                <input type="radio" name="agrupado" value="conceptos" id="agrupado_conceptos" onchange="" >
                <label>Conceptos</label>
            </div>
            <div style="margin-top:20px;margin-left:18px;" id="div_discriminar_planillas">
                <input type="checkbox" id="discrimina_planillas_liquidacion" > Discriminar planillas
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
        value      : "<?php echo $fechaInicial; ?>"
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


    if (typeof(localStorage.sucursal_liquidacion)!="undefined") {
        if (localStorage.sucursal_liquidacion!="") {
            setTimeout(function(){document.getElementById("filtro_sucursal_liquidacion").value=localStorage.sucursal_liquidacion;},100);
        }
    }

    if (typeof(localStorage.MyInformeFiltroFechaInicioLiquidacion)!="undefined") {
        if (localStorage.MyInformeFiltroFechaInicioLiquidacion!="") {
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioLiquidacion;
        }
    }

    if (typeof(localStorage.MyInformeFiltroFechaFinalLiquidacion)!="undefined") {
        if (localStorage.MyInformeFiltroFechaFinalLiquidacion!="") {
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalLiquidacion;
        }
    }

    if (typeof(localStorage.agrupacion_liquidacion)!="undefined") {
        if (localStorage.agrupacion_liquidacion!="") {
            if (localStorage.agrupacion_liquidacion=="empleados") {
                document.getElementById("agrupado_empleados").checked = true;
            }
            else if (localStorage.agrupacion_liquidacion=="conceptos") {
                document.getElementById("agrupado_conceptos").checked = true;
            }

        }
        else{
            document.getElementById("agrupado_empleados").checked = true;
        }
    }
    else{
        document.getElementById("agrupado_empleados").checked = true;
    }

    if (typeof(localStorage.discrimina_planillas_liquidacion)!="undefined") {
        if (localStorage.discrimina_planillas_liquidacion!="") {
            var check = (localStorage.discrimina_planillas_liquidacion=="true")? true : false ;
            document.getElementById("discrimina_planillas_liquidacion").checked=check;
        }
    }

    if (typeof(localStorage.tipo_contrato_liquidacion)!="undefined") {
        if (localStorage.tipo_contrato_liquidacion!="") {
            document.getElementById("tipo_contrato").value=localStorage.tipo_contrato_liquidacion;
        }
    }


    //CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
    for ( i = 0; i < arrayEmpleadosLiquidacion.length; i++) {
        if (typeof(arrayEmpleadosLiquidacion[i])!="undefined" && arrayEmpleadosLiquidacion[i]!="") {

            //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_empleado_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("bodyTablaConfiguracion").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_empleado_"+i).innerHTML=arrayEmpleadosConfiguradosLiquidacion[i];

        }
    }
    //CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS VENDEDORES GUARDADOS
    for ( i = 0; i < arrayConceptosLiquidacion.length; i++) {
        if (typeof(arrayConceptosLiquidacion[i])!="undefined" && arrayConceptosLiquidacion[i]!="") {

            //CREAMOS EL DIV DE LA FILA EN LA TABLA DE CONFIGURAR
            var div   = document.createElement("div");
            div.setAttribute("id","fila_concepto_"+i);
            div.setAttribute("class","filaBoleta");
            document.getElementById("bodyTablaConfiguracionVendedores").appendChild(div);

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById("fila_concepto_"+i).innerHTML=arrayConceptosConfiguradosLiquidacion[i];

        }
    }


</script>