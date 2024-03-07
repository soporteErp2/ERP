<?php
    include('../../../../configuracion/conectar.php');
    $id_empresa = $_SESSION['EMPRESA'];

    // CONSULTAR SI TIENE CONEXION SIHO PARA MOSTRAR OTRO FILTRO
    $sql   = "SELECT COUNT(id) AS siho FROM web_service_software WHERE id_empresa='$id_empresa' AND activo=1";
    $query = $mysql->query($sql,$mysql->link);
    $siho  = $mysql->result($query,0,'siho');

    $filtro = ($siho<=0)? 'style="display:none;" ' : '' ;

 ?>
<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(100% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .sub-content [data-width="input"]{width: 150px;}
</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR TERCERO</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Proveedor" onclick="ventanaBusquedaTercero();"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">

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

        <div class="title" <?php echo $filtro; ?> >SOFTWARE</div>
        <p <?php echo $filtro; ?> >
            <select data-width="input" id="software">
              <option value="global">Todos</option>
              <option value="ERP">ERP</option>
              <option value="SIHO">SIHO</option>
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
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
    });

    if (typeof(localStorage.sucursal_recibo_caja)!="undefined")
        if (localStorage.sucursal_recibo_caja!="")
            setTimeout(function(){document.getElementById("filtro_sucursal_recibo_caja").value=localStorage.sucursal_recibo_caja;},100);

    if (typeof(localStorage.MyInformeFiltroFechaInicioReciboCaja)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioReciboCaja!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioReciboCaja;

    if (typeof(localStorage.MyInformeFiltroFechaFinalReciboCaja)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalReciboCaja!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalReciboCaja;

    // //RECORRER EL ARRAY PARA RENDERIZAR LOS PROVEEDORES DEL FILTRO
    tercerosConfiguradosRC.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

</script>
