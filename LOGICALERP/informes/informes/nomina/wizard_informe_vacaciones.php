<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(100% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 130px;}

</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR EMPLEADOS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaGrillas();"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

    <!-- <div class="title">FILTRAR POR CENTROS DE COSTOS</div>
    <div class="content-grilla-filtro">
        <div class="head">
            <div class="cell" data-col="1"></div>
            <div class="cell" data-col="2">Codigo</div>
            <div class="cell" data-col="3">Nombre</div>
            <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCcos();"></div>
        </div>
        <div class="body" id="body_grilla_filtro_ccos">
        </div>
    </div> -->

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">DETALLE DE INFORME</div>
        <p>
            <select data-width="input" id="detalle" onchange="cambia_filtro()">
                <option value="todos">Todos los registros</option>
                <option value="rango_fechas">Rango de fechas</option>
            </select>
        </p>

        <div id="filtro_fechas">
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
        </div>


    </div>
</div>
<script>
    var rows = '';
    cambia_filtro();

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
    });

    function cambia_filtro() {
        var detalle = document.getElementById('detalle').value

        if (detalle=='rango_fechas') { document.getElementById('filtro_fechas').style.visibility = "visible"; }
        else{ document.getElementById('filtro_fechas').style.visibility = "hidden";}
    }
    // document.getElementById("cuenta_inicial").value=(typeof(localStorage.cuenta_inicialLA )!="undefined")? localStorage.cuenta_inicialLA  : "" ;
    // document.getElementById("cuenta_final").value=(typeof(localStorage.cuenta_finalLA )!="undefined")? localStorage.cuenta_finalLA  : "" ;

    // if (typeof(localStorage.sucursal_libro_auxiliar)!="undefined")
    //     if (localStorage.sucursal_libro_auxiliar!="")
    //         setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_libro_auxiliar").value=localStorage.sucursal_libro_auxiliar;},100);

    // if (typeof(localStorage.MyInformeFiltroFechaInicioLibroAuxiliar)!="undefined")
    //     if (localStorage.MyInformeFiltroFechaInicioLibroAuxiliar!="")
    //         document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioLibroAuxiliar;

    // if (typeof(localStorage.MyInformeFiltroFechaFinalLibroAuxiliar)!="undefined")
    //     if (localStorage.MyInformeFiltroFechaFinalLibroAuxiliar!="")
    //         document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalLibroAuxiliar;

    // if (typeof(localStorage.totalizado_libro_auxiliar)!="undefined")
    //     if (localStorage.totalizado_libro_auxiliar!="")
    //         document.getElementById("totalizado").value=localStorage.totalizado_libro_auxiliar;

    // if (typeof(localStorage.order_libro_auxiliar)!="undefined")
    //     if (localStorage.order_libro_auxiliar!="")
    //         document.getElementById("order").value=localStorage.order_libro_auxiliar;

    // if (typeof(localStorage.by_libro_auxiliar)!="undefined")
    //     if (localStorage.by_libro_auxiliar!="")
    //         document.getElementById("by").value=localStorage.by_libro_auxiliar;

    // if (typeof(localStorage.mostrar_observacion)!="undefined")
    //     if (localStorage.mostrar_observacion!="")
    //         document.getElementById("mostrar_observacion").value=localStorage.mostrar_observacion;


    // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    arrayEmpleadosConfiguradosVacaciones.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    // rows = '';
    // // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    // centroCostosConfiguradosLA.forEach(function(elemento) {rows += elemento;});
    // document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;

</script>