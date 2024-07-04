<?php
    $date = strtotime(date("Y-m-d"));
    $anio = date("Y", $date);
    $mes  = date("m", $date);
    $dia  = date("d",$date);

    $fechaInicial=date("Y-m-d",(mktime(0,0,0,$mes,1,$anio)-1));

?>
<div style="width:100%; height:100%; border-top:1px solid #8DB2E3; overflow:hidden;">
    <div style="float:left; width:50%; height:100%; padding:10px; border-right:1px solid #8DB2E3; box-sizing:border-box;">
        <div style="font-weight: bolder;font-size:12px; margin-bottom:15px; text-align:center;">Tipo de Balance</div>
        <div>
            <input type="radio" name="tipo_balance" value="clasificado" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
            <label>Balance con corte</label>
        </div>
        <div style="margin-top:10px;">
            <input type="radio" name="tipo_balance" value="comparativo" id="tipo_balance" onchange="mostrarOcultarDivFechaInicio(this.value);">
            <label>Balance rango de fechas</label>
        </div>
        <div style="font-weight: bolder;font-size:12px; margin-bottom:15px; margin:15px 0; text-align:center; display:none;">Rango</div>
        <div style="text-align:center; display:none;">
            <select id="rango_balance" style="width:100px;">
                <option value="anual">Anual</option>
                <option value="mensual">Mensual</option>
            </select>
        </div>
    </div>
    <div style="float:left; width:50%; height:100%; padding:10px; text-align:center; box-sizing:border-box;">
        <div style="font-weight: bolder;font-size:12px ;margin-bottom:10px;">Nivel de las cuentas</div>
        <div>
            <select id="nivel_cuenta" style="width:100px;">
                <option value="Grupos">Grupo</option>
                <option value="Cuentas">Cuenta</option>
                <option value="Subcuentas">Subcuenta</option>
                <option value="Auxiliares">Auxiliar</option>
            </select>
        </div>

        <div style="float:left;width:100%; margin-bottom:15px; margin-top:40px;text-align:center;font-weight: bolder;font-size:12px;">Fechas del Informe</div>
        <div style="display:table; text-align:center; margin:auto;">
            <div id="divFechaInicio">
                Periodo Inicial:<br>
                <input type="text" id="MyInformeFiltroFechaInicio"/>
            </div>

            <div style="margin-top:10px;">
                Periodo final:<br>
                <input type="text" id="MyInformeFiltroFechaFinal"/>
            </div>
        </div>
    </div>
</div>



<script>
    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        id         :"cmpFechaInicio",
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaInicio",
        editable   : false,
        value      : "<?php echo $fechaInicial ?>"
    });

    new Ext.form.DateField({
        format     : "Y-m-d",
        width      : 120,
        allowBlank : false,
        showToday  : false,
        applyTo    : "MyInformeFiltroFechaFinal",
        editable   : false,
        value      : new Date(),
        // listeners  : { select: function() {   } }
    });

    var elementos = document.getElementsByName("tipo_balance");

    //SI LAS VARIABLES LOCALSTORAGE TIENEN VALORES, ENTONCES MOSTRAR EN LA CONFIGURACION DE IMPRESION DEL INFORME ESAS VARIABLES
    //&& localStorage.MyInformeFiltroFechaFinal!="" && localStorage.generar!=""
    if ( typeof(localStorage.tipo_balance)!="undefined" && localStorage.tipo_balance!="") {

        for(var i=0; i<elementos.length; i++) {
            if (elementos[i].value==localStorage.tipo_balance) {tipo_balance=elementos[i].checked=true;}
        }

        document.getElementById("nivel_cuenta").value=localStorage.generar;
        document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinal;

        if (localStorage.tipo_balance=="comparativo") {
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicio;
        }
    }

    if (localStorage.tipo_balance=="clasificado") {
        document.getElementById("divFechaInicio").style.display="none";
        elementos[0].checked=true;
    }
    else if (localStorage.tipo_balance=="comparativo") {
        document.getElementById("divFechaInicio").style.display="block";
        elementos[1].checked=true;
    }else{
        document.getElementById("divFechaInicio").style.display="none";
        elementos[0].checked=true;
    }

    function mostrarOcultarDivFechaInicio(value){

        if (value=="clasificado") { document.getElementById("divFechaInicio").style.display="none"; }
        else if (value=="comparativo") { document.getElementById("divFechaInicio").style.display="block"; }
        else{ Win_Ventana_configurar_balance_general.close(); }
    }

</script>
<script>
</script>