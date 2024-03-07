<?php
    include('../../../../../configuracion/conectar.php');
    include('../../../../../configuracion/define_variables.php');

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

<div class="main-content" id="content-wizard">
    <?php
        if (!isset($id_formato) || $id_formato=='' || $id_formato==0) {
            echo "<div style='text-align: center;font-size: 18px;color: #1e82c4;padding-top:20px;'>Seleccione el Informe para continuar </div>";
            exit;
        }

        // CONSULTAR LOS FILTRO QUE SE APLICARAN PARA EL INFORME
        $sql="SELECT
                filtro_terceros,
                filtro_ccos,
                filtro_corte_anual,
                filtro_corte_mensual,
                filtro_rango_fechas,
                filtro_cuentas
                FROM informes_niif_formatos WHERE activo=1 AND id=$id_formato ";
        $query=$mysql->query($sql,$mysql->link);

        $filtro_terceros      = $mysql->result($query,0,'filtro_terceros');
        $filtro_ccos          = $mysql->result($query,0,'filtro_ccos');
        $filtro_corte_anual   = $mysql->result($query,0,'filtro_corte_anual');
        $filtro_corte_mensual = $mysql->result($query,0,'filtro_corte_mensual');
        $filtro_rango_fechas  = $mysql->result($query,0,'filtro_rango_fechas');
        $filtro_cuentas       = $mysql->result($query,0,'filtro_cuentas');

     ?>
    <div class="sub-content" data-position="right">
        <?php
            // SI TIENE HABILITADO EL FILTRO DE TERCEROS
            if ($filtro_terceros=='Si') {
         ?>
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
        <?php
            }
            if ($filtro_ccos=='Si') {
         ?>

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

        <?php
            }
         ?>
       </div>

    <div class="sub-content" data-position="left">
        <div class="title">FORMATO DE DIGITOS</div>
        <p>
            <table>
                <tr>
                    <td>Separador miles</td>
                    <td>
                        <select id="separador_miles" data-width="input">
                            <option value=".">Punto (.)</option>
                            <option value=",">Coma (,)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Separador decimales</td>
                    <td>
                        <select id="separador_decimales" data-width="input">
                            <option value=",">Coma (,)</option>
                            <option value=".">Punto (.)</option>
                        </select>
                    </td>
                </tr>
            </table>
        </p>

        <?php
            if ($filtro_corte_mensual=='Si') {
        ?>
            <div class="title">MES DEL INFORME</div>
            <p>
                <table>
                    <tr>
                        <td>Fecha de corte</td>
                        <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
                    </tr>
                </table>
            </p>

        <?php
            }
            if ($filtro_corte_anual=='Si') {
        ?>
            <div class="title">A&Ntilde;O DEL INFORME</div>
            <p>
                <table>
                    <tr>
                        <td>Fecha de corte</td>
                        <td><input type="text" id="MyInformeFiltroFechaFinal"/></td>
                    </tr>
                </table>
            </p>
        <?php
            }
            if ($filtro_rango_fechas=='Si') {
        ?>
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
        <?php
            }
            if ($filtro_cuentas=='Si') {
         ?>
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
        <?php
        }
        ?>
    </div>
</div>
<script>
    var rows = '';

    if (typeof(localStorage.separador_milesNiifR)!="undefined")
        if (localStorage.separador_milesNiifR!="")
            document.getElementById("separador_miles").value=localStorage.separador_milesNiifR;

    if (typeof(localStorage.separador_decimalesNiifR)!="undefined")
        if (localStorage.separador_decimalesNiifR!="")
            document.getElementById("separador_decimales").value=localStorage.separador_decimalesNiifR;

    <?php
        if ($filtro_corte_mensual=='Si' || $filtro_corte_anual=='Si' ) {
    ?>
        new Ext.form.DateField({
            format     : "Y-m-d",
            width      : 120,
            id         :"cmpFechaInicio",
            allowBlank : false,
            showToday  : false,
            applyTo    : "MyInformeFiltroFechaFinal",
            editable   : false,
            // value      : "'.$fechaInicial.'"
            // listeners  : { select: function() {   } }
        });

        if (typeof(localStorage.MyInformeFiltroFechaFinalNiifR)!="undefined")
            if (localStorage.MyInformeFiltroFechaFinalNiifR!="")
                document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalNiifR;
    <?php
        }
        if ($filtro_rango_fechas=='Si') {
    ?>

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

    if (typeof(localStorage.MyInformeFiltroFechaInicioNiifR)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioNiifR!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioNiifR;

    if (typeof(localStorage.MyInformeFiltroFechaFinalNiifR)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalNiifR!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalNiifR;

    <?php
        }
        if ($filtro_terceros=='Si') {
    ?>
        // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
        tercerosConfiguradosERPR.forEach(function(elemento) {rows += elemento;});
        document.getElementById('body_grilla_filtro').innerHTML=rows;
    <?php
        }
        if ($filtro_ccos=='Si') {
    ?>
        rows = '';
        // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
        centroCostosConfiguradosERPR.forEach(function(elemento) {rows += elemento;});
        document.getElementById('body_grilla_filtro_ccos').innerHTML=rows;
    <?php
        }
    ?>


</script>