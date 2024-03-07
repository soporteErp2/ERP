<?php
    // CONSULTAR LOS GRUPOS EMPRESARIALES
    include_once('../../../../configuracion/xml2array.php');
    if (!isset($_SESSION)){session_start();}
    $DIRECTORIO = explode ("/", $_SERVER['REQUEST_URI']);

    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml')){
        $fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[1].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE LOCAL O EN CARPETA /SIIP
    }
    if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml')){
        $fichero  = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/'.$DIRECTORIO[0].'/ARCHIVOS_PROPIOS/conexion.xml'); //SI SE LLAMA DESDE UN DOMINIO
    }

    $array = xml2array($fichero);

    $servidor   = $array['configuracion']['database']['servidor'];
    $usuario    = $array['configuracion']['database']['usuario'];
    $password   = $array['configuracion']['database']['password'];
    $bd         = $array['configuracion']['database']['bd'];
    // var_dump($password);
    $globalAccess = mysql_connect($servidor,$usuario,$password);
    if(!$globalAccess){ echo 'Error Conectando a Mysql<br />'; };
    mysql_select_db($bd,$globalAccess);
    if(!@mysql_select_db($bd,$globalAccess)){ echo 'Error Conectando a la la base de datos "'.$bd.'" <br />'; };

    $sql   = "SELECT nit,nombre,id_grupo_empresarial FROM host WHERE activo=1 ";
    $query = mysql_query($sql,$globalAccess);
    while ($row=mysql_fetch_array($query)) {
        $arrayEmpresasGrupo[$row['nit']]  = array(
                                                    'nombre'               => $row['nombre'],
                                                    'id_grupo_empresarial' => $row['id_grupo_empresarial'],
                                                );
    }

    $nitEmpresa = explode("-", $_SESSION['NITEMPRESA']);
    $sql   = "SELECT id,nombre FROM grupos_empresariales WHERE activo=1 ";
    $query = mysql_query($sql,$globalAccess);
    while ($row=mysql_fetch_array($query)) {
        $arrayGrupo[$row['id']] = $row['nombre'];
    }
    mysql_close($globalAccess);
    // var_dump($mysql);
    // var_dump($arrayEmpresasGrupo);

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');

 ?>

<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
    .content-grilla-filtro .cell[data-col="4"]{width: 220px;}
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
                filtro_corte_mensual,
                filtro_rango_fechas,
                filtro_cuentas
                FROM informes_formatos WHERE activo=1 AND id=$id_formato ";
        $query=$mysql->query($sql,$mysql->link) or die(mysql_error());;

        // var_dump($query);
        $filtro_terceros      = $mysql->result($query,0,'filtro_terceros');
        $filtro_ccos          = $mysql->result($query,0,'filtro_ccos');
        $filtro_corte_mensual = $mysql->result($query,0,'filtro_corte_mensual');
        $filtro_rango_fechas  = $mysql->result($query,0,'filtro_rango_fechas');
        $filtro_cuentas       = $mysql->result($query,0,'filtro_cuentas');

     ?>
    <div class="sub-content" data-position="right">
        <?php
            // SI TIENE GRUPOS EMPRESARIALES
            if ($arrayEmpresasGrupo[$nitEmpresa[0]]['id_grupo_empresarial']>0) {
            ?>
            <div class="title">EMPRESAS DEL GRUPO EMPRESARIAL <b><?= $arrayGrupo[$arrayEmpresasGrupo[$nitEmpresa[0]]['id_grupo_empresarial']]; ?></b> </div>
            <div class="content-grilla-filtro">
                <div class="head">
                    <div class="cell" data-col="1"></div>
                    <div class="cell" data-col="2">Documento</div>
                    <div class="cell" data-col="4">Nombre</div>
                </div>
                <div class="body" id="body_grilla_grupos">
                    <?php

                        foreach ($arrayEmpresasGrupo as $nit => $arrayResult){
                            if ($arrayResult['id_grupo_empresarial']==$arrayEmpresasGrupo[$nitEmpresa[0]]['id_grupo_empresarial'] && $nitEmpresa[0]<>$nit ) {
                            ?>
                                <div id="row_tercero_1" class="row">
                                    <div class="row" id="row_tercero_1">
                                       <div class="cell" data-col="1"></div>
                                       <div class="cell" data-col="2"><?= $nit ?></div>
                                       <div class="cell" data-col="4" title="<?= $arrayResult['nombre'] ?>"><?= $arrayResult['nombre'] ?></div>
                                       <div class="cell" data-col="1" data-icon="" onclick="" title="Seleccionar empresa"> <input type="checkbox" class="checkboxGroup" data-nit="<?= $nit ?>" data-nombre="<?= $arrayResult['nombre'] ?>"></div>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                    ?>
                </div>
            </div>
            <?php
            }
        ?>
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
                        <td><input type="text" id="MyInformeFiltroFechaInicio"/></td>
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

    if (typeof(localStorage.separador_miles)!="undefined")
        if (localStorage.separador_miles!="")
            document.getElementById("separador_miles").value=localStorage.separador_miles;

    if (typeof(localStorage.separador_decimales)!="undefined")
        if (localStorage.separador_decimales!="")
            document.getElementById("separador_decimales").value=localStorage.separador_decimales;

    <?php
        if ($filtro_corte_mensual=='Si') {
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

        if (typeof(localStorage.MyInformeFiltroFechaInicioERPR)!="undefined")
            if (localStorage.MyInformeFiltroFechaInicioERPR!="")
                document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioERPR;
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

    if (typeof(localStorage.MyInformeFiltroFechaInicioERPR)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioERPR!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioERPR;

    if (typeof(localStorage.MyInformeFiltroFechaFinalERPR)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalERPR!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalERPR;

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

    var cargarInformeEmpresaGrupo = (params)=>{
        Ext.get(`content-${params.nit}`).load({
            url     : '../informes/informes/report/controller.php',
            text    : 'Generando Informe...',
            scripts : true,
            nocache : true,
            timeout : 99000000,
            params  :
            {
                nit                        : `${params.nit}`,
                id_formato                 : `${params.id_formato}`,
                MyInformeFiltroFechaInicio : `${params.fechaInicio}`,
                MyInformeFiltroFechaFinal  : `${params.fechaFinal}`,
                separador_miles            : `${params.separador_miles}`,
                separador_decimales        : `${params.separador_decimales}`,
                sucursal                   : `${params.sucursal}`,
                arrayCentroCostosJSON      : `${params.arrayCentroCostosJSON}`,
                arrayGrupoJSON             : ``,
                empresaGrupo               : true

            }
        });
    }

    var ventanaCuentasSeccion = (params)=>{
        // console.log(params);
        // console.log(JSON.stringify(params));
        // console.log(typeof(JSON.stringify(params)));
        Win_Ventana_cuentas_seccion = new Ext.Window({
            width       : 700,
            height      : 500,
            id          : 'Win_Ventana_cuentas_seccion',
            title       : 'Cuentas de la seccion',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'informes/report/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc    : 'valoresCuentasSeccion',
                    params : JSON.stringify(params)
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_cuentas_seccion.close(id) }
                }
            ]
        }).show();
    }


</script>