<?php

    include_once('../../../../configuracion/conectar.php');
    include_once('../../../../configuracion/define_variables.php');
    $id_empresa = $_SESSION['EMPRESA'];

    $sql="SELECT id,codigo FROM medios_magneticos_formatos WHERE activo=1 AND id_empresa=$id_empresa";
    $query=$mysql->query($sql,$mysql->link);
    while ($row=mysql_fetch_array($query)) {
        $options.='<option value="'.$row['id'].'" >'.$row['codigo'].'</option>';
    }

?>



<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha">
        <div class="WizTitulo">Asistente Generador de Medios Magneticos</div>
        <div  class="WizContenido">
            <b>Seleccione el Formato</b>
            <br /><br />
            <select class="myfield" id="id_formato" style="width:120px">
              <?php echo $options; ?>
            </select>
        </br>
        </br>
            <b>A&ntilde;o Gravable</b>
            <br />
            <br />
            <input type="text" class="myfield" id="anio_gravable"  style="width:170px;height:24px" onKeyup="validar_numero_moneda(event,this);"/>
        </br>
        </br>
        <input type="button" value="Generar >>" onClick="generaFormato();" style="width:100px; height:30px;"/>
        <input type="button" value="Generar excel>>" onClick="generaFormato('excel');" style="width:100px; height:30px;"/>
        </div>
    </div>
</div>
<script>
    new Ext.form.DateField({
        format     : 'Y',               //FORMATO
        width      : 130,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'anio_gravable',
        editable   : false,                 //EDITABLE
        // value      : new Date(),             //VALOR POR DEFECTO
        value      : '2015',             //VALOR POR DEFECTO
    });
    function generaFormato(opc){
        var id_formato = document.getElementById('id_formato').value
        ,   fecha      = document.getElementById('anio_gravable').value;

        if (opc=='excel') { generarExcel(id_formato,fecha); }
        else{generarHtml( id_formato,fecha );
}

    }
</script>