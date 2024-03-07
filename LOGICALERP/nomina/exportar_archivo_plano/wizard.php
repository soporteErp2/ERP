<?php

    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');
    $id_empresa = $_SESSION['EMPRESA'];

    $sql="SELECT id,codigo FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=mysql_fetch_array($query)) {
        $options.='<option value="'.$row['id'].'" >'.$row['codigo'].'</option>';
    }

?>



<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha" style="width:340px;">
        <div class="WizTitulo">Asistente Generador de Archivo Plano</div>
        <div  class="WizContenido">
            <table>
                <tr style='font-family: "Trebuchet MS", Verdana, Arial, sans-serif, "Lucida Grande";font-size: 12px;font-weight:bold;'>
                    <td>Pension</td>
                    <td>&nbsp;</td>
                    <td>Salud</td>
                </tr>
                <tr>
                    <td><input type="text" id="periodo_pago_dif"></td>
                    <td>&nbsp;</td>
                    <td><input type="text" id="periodo_pago"></td>
                </tr>
            </table>
        </br>
        </br>
        <input type="button" value="Generar >>" onClick="generaFormato();" style="width:100px; height:30px;"/>
        <!-- <input type="button" value="Generar excel>>" onClick="generaFormato('excel');" style="width:100px; height:30px;"/> -->
        </div>
    </div>
</div>
<script>

    new Ext.form.DateField({
        format     : 'Y-m',               //FORMATO
        width      : 130,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'periodo_pago_dif',
        editable   : false,                 //EDITABLE
    });

    new Ext.form.DateField({
        format     : 'Y-m',               //FORMATO
        width      : 130,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'periodo_pago',
        editable   : false,                 //EDITABLE
    });

    if (typeof(localStorage.periodo_pago_dif)!="undefined")
        if (localStorage.periodo_pago_dif!="")
            document.getElementById('periodo_pago_dif').value=localStorage.periodo_pago_dif

    if (typeof(localStorage.periodo_pago)!="undefined")
        if (localStorage.periodo_pago!="")
            document.getElementById('periodo_pago').value=localStorage.periodo_pago


    function generaFormato(opc){
        var periodo_pago_dif = document.getElementById('periodo_pago_dif').value
        ,   periodo_pago      = document.getElementById('periodo_pago').value;

        if (periodo_pago_dif=='' || periodo_pago=='') { alert("Debe seleccionar las fechas!"); return; };

        localStorage.periodo_pago_dif = periodo_pago_dif;
        localStorage.periodo_pago     = periodo_pago;
        
        window.open("exportar_archivo_plano/class.exportFile.php?periodo_pago_dif="+periodo_pago_dif+"&periodo_pago="+periodo_pago);

    }
</script>