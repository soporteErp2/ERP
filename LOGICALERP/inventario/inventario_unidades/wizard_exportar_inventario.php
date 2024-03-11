<?php

    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');
    $id_empresa = $_SESSION['EMPRESA'];

?>



<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha" style="width: 300px;">
        <div class="WizTitulo">Asistente Generador excel inventario</div>
        <div  class="WizContenido">
            <b>Separador de decimales</b>
            <br />
            <br />
            <select id="separadorDecimales" onChange="validarSelect('decimales');" style="width: 130px;">
                <option value=".">Punto (.)</option>
                <option value=",">Coma (,)</option>
            </select>
        </br>
        </br>
        <b>Separador de miles</b>
            <br />
            <br />
            <select id="separadorMiles" onChange="validarSelect('miles');" style="width: 130px;">
                <option value=",">Coma (,)</option>
                <option value=".">Punto (.)</option>
                <option value="">Sin separador</option>
            </select>
        </br>
        </br>
        <!-- <input type="button" value="Generar >>" onClick="generaFormato();" style="width:100px; height:30px;"/> -->
        <input type="button" value="Generar excel>>" onClick="generarInventarioExcel();" style="width:110px; height:30px;"/>
        </div>
    </div>
</div>