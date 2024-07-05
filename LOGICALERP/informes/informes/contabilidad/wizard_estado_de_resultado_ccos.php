<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(70% - 3px); }
    .sub-content[data-position="left"]{width: 30%; overflow:auto;}
    .content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 195px;overflow:hidden; white-space:nowrap; text-overflow: ellipsis;}
    .content-grilla-filtro .cell[data-col="4"]{width: 58px; text-align: right;border-right: none;}
    .sub-content [data-width="input"]{width: 120px;}

</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR TERCEROS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Terceros" onclick="ventanaBusquedaTercero();"></div>
                <div class="cell" data-col="4" data-icon="un_checked" onclick="cambiaCheckERC(this);" id="div_check_terceros"> <span>Todos </span> </div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

        <div class="title">FILTRAR POR CENTROS DE COSTOS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Codigo</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Centro Costos" onclick="ventanaBusquedaCcos();"></div>
                <div class="cell" data-col="4" data-icon="un_checked" onclick="cambiaCheckERC(this);" id="div_check_ccos"> <span>Todos </span> </div>
            </div>
            <div class="body" id="body_grilla_filtro_ccos">
            </div>
        </div>
    </div>

    <div class="sub-content" data-position="left">
        <div class="title">NIVEL DE CUENTAS</div>
        <p>
            <select data-width="input" id="nivel_cuenta">
                <option value="Cuentas">Cuentas</option>
                <option value="Subcuentas">SubCuentas</option>
                <option value="Auxiliares">Auxiliares</option>
            </select>
        </p>
        <div class="title">TIPO DE INFORME</div>
        <p>
            <select data-width="input" id="tipo_informe">
                <option value="mensual">Mensual</option>
                <option value="mensual_acumulado">Mensual Acumulado</option>
                <option value="comparativo_mensual">Comparativo Mensual</option>
                <option value="comparativo_anual">Comparativo Anual</option>
            </select>
        </p>
        <div class="title">FECHAS DEL INFORME</div>
        <p>
            <table>
                <tr>
                    <td>Fecha Final</td>
                    <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
                </tr>
            </table>
        </p>

    </div>
</div>
<script>
    var rows = '';

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 100,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
        // value      : new Date(),
        // listeners  : { select: function() {   } }
    });

    rows = '';
    // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    centroCostosConfiguradosERC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;

    rows = '';
    // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    tercerosConfiguradosERC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    if (typeof(localStorage.nivel_cuentas_EstadoResultadoC)!="undefined")
        if (localStorage.nivel_cuentas_EstadoResultadoC!="")
            document.getElementById("nivel_cuenta").value=localStorage.nivel_cuentas_EstadoResultadoC;

    if (typeof(localStorage.tipo_balance_EstadoResultadoC)!="undefined")
        if (localStorage.tipo_balance_EstadoResultadoC!="")
            document.getElementById("tipo_informe").value=localStorage.tipo_balance_EstadoResultadoC;

    // if (typeof(localStorage.MyInformeFiltroFechaInicioEstadoResultadoC)!="undefined")
        // if (localStorage.MyInformeFiltroFechaInicioEstadoResultadoC!="")
            // document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioEstadoResultadoC;

    if (typeof(localStorage.MyInformeFiltroFechaFinalEstadoResultadoC)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalEstadoResultadoC!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalEstadoResultadoC;

    if (typeof(localStorage.sucursales_estado_resultadoC)!="undefined")
        if (localStorage.sucursales_estado_resultadoC!="")
           if(document.getElementById("filtro_sucursal_sucursales_estado_resultado"))
                document.getElementById("filtro_sucursal_sucursales_estado_resultado").value=localStorage.sucursales_estado_resultadoC;

    if (checkBoxSelectAllERC=="true")
        document.getElementById('div_check_ccos').dataset.icon='check';

    if (checkBoxSelectAllTercerosERC=="true")
        document.getElementById('div_check_terceros').dataset.icon='check';

    function cambiaCheckERC(element) {
        if (element.getAttribute("data-icon")=="un_checked") {
            element.dataset.icon="check";
            if(element.id=='div_check_ccos'){checkBoxSelectAllERC="true"; }
            else{ checkBoxSelectAllTercerosERC = 'true'; }

        }
        else{
            element.dataset.icon="un_checked";
            if(element.id=='div_check_ccos'){checkBoxSelectAllERC="false"; }
            else{ checkBoxSelectAllTercerosERC = 'false'; }
        }


    }


</script>