<?php 

    include('../../../../configuracion/conectar.php');
    include('../../../../configuracion/define_variables.php');

    $id_empresa  = $_SESSION['EMPRESA'];

    // CONSULTAR LOS TIPOS DE CONTRATOS
    $sql="SELECT id,descripcion FROM nomina_tipo_contrato WHERE activo=1 AND id_empresa=$id_empresa";
    $query = $mysql->query($sql,$mysql->link);
    while ($row=$mysql->fetch_array($query)) {
        $selected = ($tipo_contrato==$row['id'])? 'selected' : '' ;
        $optionContratos .= "<option value='$row[id]' $selected >$row[descripcion]</option>";
    }
 
 ?>
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
        <div class="title">FILTRAR POR EMPLEADOS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Empleados" onclick="ventanaBusquedaGrillas('empleados');"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

    <div class="title">FILTRAR POR CONCEPTOS</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Codigo</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Concepto" onclick="ventanaBusquedaGrillas();"></div>
            </div>
            <div class="body" id="body_grilla_filtro_conceptos">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">TIPO DE CONTRATO</div>
        <p>
            <select data-width="input" id="tipo_contrato">
                <option value="todos">Todos</option>
                <?php echo $optionContratos; ?>
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
        <div class="title">AGRUPAR POR</div>
        <p>
            <select data-width="input" id="agrupado">
                <option value="Empleados">Empleados</option>
                <option value="Conceptos">Conceptos</option>
            </select>
        </p>
        <div class="title">DISCRIMINAR PLANILLAS</div>
        <p>
            <select data-width="input" id="discrimina_planillas">
                <option value="No">No</option>
                <option value="Si">Si</option>
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

    //if (typeof(localStorage.sucursal_libro_auxiliar)!="undefined")
    //    if (localStorage.sucursal_libro_auxiliar!="")
    //        setTimeout(function(){document.getElementById("filtro_sucursal_sucursales_libro_auxiliar").value=localStorage.sucursal_libro_auxiliar;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioNomina)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioNomina!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioNomina;

    if (typeof(localStorage.MyInformeFiltroFechaFinalNomina)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalNomina!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalNomina;

    if (typeof(localStorage.agrupacion_nomina)!="undefined")
        if (localStorage.agrupacion_nomina!="")
            document.getElementById("agrupado").value=localStorage.agrupacion_nomina;

    if (typeof(localStorage.discrimina_planillas)!="undefined")
        if (localStorage.discrimina_planillas!="")
            document.getElementById("discrimina_planillas").value=localStorage.discrimina_planillas;

    //if (typeof(localStorage.by_libro_auxiliar)!="undefined")
    //    if (localStorage.by_libro_auxiliar!="")
    //        document.getElementById("by").value=localStorage.by_libro_auxiliar;

    //if (typeof(localStorage.mostrar_observacion)!="undefined")
    //    if (localStorage.mostrar_observacion!="")
    //        document.getElementById("mostrar_observacion").value=localStorage.mostrar_observacion;


    // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    arrayEmpleadosNomina.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    rows = '';
    // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    arrayConceptosNomina.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_conceptos').innerHTML=rows;

</script>