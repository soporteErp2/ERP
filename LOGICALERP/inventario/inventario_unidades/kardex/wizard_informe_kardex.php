<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    $id_empresa = $_SESSION['EMPRESA'];

?>



<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha" style="width: 300px;">
        <div class="WizTitulo">Asistente Generador de Informes</div>
        <div  class="WizContenido">
            <b>Fecha de Corte</b>
            <br />
            <br />
            <input type="text" class="myfield" id="fecha_corte"  style="width:170px;height:24px" onKeyup="validar_numero_moneda(event,this);"/>
        </br>
        </br>
            <b>Detallar Documentos</b>
            <br />
            <br />
            <select id="detallado" style="width: 130px;">
                <option value="No">No</option>
                <option value="Si">Si</option>
            </select>
        </br>
        </br>
        <!-- <input type="button" value="Generar >>" onClick="generaFormato();" style="width:100px; height:30px;"/> -->
        <input type="button" value="Generar excel>>" onClick="generarInformeKardex('excel');" style="width:110px; height:30px;"/>
        </div>
    </div>
</div>
<script>
    new Ext.form.DateField({
        format     : 'Y-m-d',               //FORMATO
        width      : 130,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_corte',
        editable   : false,                 //EDITABLE
        // value      : new Date(),             //VALOR POR DEFECTO
        value      : '2017-12-31',             //VALOR POR DEFECTO
    });

</script>