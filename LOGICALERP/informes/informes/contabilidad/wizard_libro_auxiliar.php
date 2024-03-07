<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
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
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div>
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
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCcos();"></div>
            </div>
            <div class="body" id="body_grilla_filtro_ccos">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">TOTALIZADO</div>
        <p>
            <select data-width="input" id="totalizado">
                <option value="cuentas">Por Cuentas</option>
                <option value="terceros">Por Terceros</option>
            </select>
        </p>
        <div class="title">ORDENAR</div>
        <p>
            <select data-width="input" id="order">
                <option value="fecha">Por Fecha</option>
                <option value="tercero">Alfabeticamente</option>
            </select>
            <br>
            <br>
            <select data-width="input" id="by">
                <option value="DESC">Descendente</option>
                <option value="ASC">Ascendente</option>
            </select>
        </p>
        <div class="title">FECHAS DEL INFORME</div>
        <p>
            <table>
                <tr>
                    <td>Fecha Inicial</td>
                    <td><input type="text" id="MyInformeFiltroFechaInicio"/></td>
                </tr>
                <tr>
                    <td>Fecha Final</td>
                    <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
                </tr>
            </table>
        </p>
        <div class="title">RANGO DE CUENTAS</div>
        <p>
            <table>
                <tr>
                    <td>Cuenta Inicial</td>
                    <td><input type="text" id="cuenta_inicial" data-width="input" onkeyup="validaCuentaPuc(event,this);" /></td>
                    <td data-icon="btn-search" onclick="ventanaBuscarCuentaPuc('cuenta_inicial')"></td>
                </tr>
                <tr>
                    <td>Cuenta Final</td>
                    <td><input type="text" id="cuenta_final" data-width="input" onkeyup="validaCuentaPuc(event,this);"/></td>
                    <td data-icon="btn-search" onclick="ventanaBuscarCuentaPuc('cuenta_final')"></td>
                </tr>
            </table>
        </p>
        <div class="title">OBSERVACIONES DE LOS ASIENTOS</div>
        <p>
            <select data-width="input" id="mostrar_observacion">
                <option value="">No mostrar</option>
                <option value=",observacion">Mostrar</option>
            </select>
        </p>

    </div>
</div>
<script>
    var rows = '';

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        id         :"cmpFechaInicio",
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaInicio",
        editable   : false,
        // value      : "'.$fechaInicial.'"
        // listeners  : { select: function() {   } }
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
        // value      : new Date(),
        // listeners  : { select: function() {   } }
    });


    document.getElementById("cuenta_inicial").value=(typeof(localStorage.cuenta_inicialLA )!="undefined")? localStorage.cuenta_inicialLA  : "" ;
    document.getElementById("cuenta_final").value=(typeof(localStorage.cuenta_finalLA )!="undefined")? localStorage.cuenta_finalLA  : "" ;

    if (typeof(localStorage.sucursal_libro_auxiliar)!="undefined")
        if (localStorage.sucursal_libro_auxiliar!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_libro_auxiliar").value=localStorage.sucursal_libro_auxiliar;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioLibroAuxiliar)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioLibroAuxiliar!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioLibroAuxiliar;

    if (typeof(localStorage.MyInformeFiltroFechaFinalLibroAuxiliar)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalLibroAuxiliar!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalLibroAuxiliar;

    if (typeof(localStorage.totalizado_libro_auxiliar)!="undefined")
        if (localStorage.totalizado_libro_auxiliar!="")
            document.getElementById("totalizado").value=localStorage.totalizado_libro_auxiliar;

    if (typeof(localStorage.order_libro_auxiliar)!="undefined")
        if (localStorage.order_libro_auxiliar!="")
            document.getElementById("order").value=localStorage.order_libro_auxiliar;

    if (typeof(localStorage.by_libro_auxiliar)!="undefined")
        if (localStorage.by_libro_auxiliar!="")
            document.getElementById("by").value=localStorage.by_libro_auxiliar;

    if (typeof(localStorage.mostrar_observacion)!="undefined")
        if (localStorage.mostrar_observacion!="")
            document.getElementById("mostrar_observacion").value=localStorage.mostrar_observacion;


    // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    tercerosConfiguradosLA.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    rows = '';
    // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    centroCostosConfiguradosLA.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;

</script>