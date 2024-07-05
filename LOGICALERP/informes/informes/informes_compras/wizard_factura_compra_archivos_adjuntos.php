<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(98% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 150px;}
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

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">FILTRAR POR CONTENIDO</div>
        <p>
            <select data-width="input" id="contenido">
              <option value="todos">Todo</option>
              <option value="conArchivos">Con archivos adjuntos</option>
              <option value="sinArchivos">Sin archivos adjuntos</option>
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
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
    });

    if (typeof(localStorage.sucursal_archivos_adjuntos)!="undefined")
        if (localStorage.sucursal_archivos_adjuntos!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_archivos_adjuntos").value=localStorage.sucursal_archivos_adjuntos;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioArchivosAdjuntos)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioArchivosAdjuntos!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioArchivosAdjuntos;

    if (typeof(localStorage.MyInformeFiltroFechaFinalArchivosAdjuntos)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalArchivosAdjuntos!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalArchivosAdjuntos;

    if (typeof(localStorage.contenido_archivos_adjuntos)!="undefined")
        if (localStorage.contenido_archivos_adjuntos!="")
            document.getElementById("contenido").value=localStorage.contenido_archivos_adjuntos;

    //RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    tercerosConfiguradosAA.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

</script>
