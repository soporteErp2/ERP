<?php

    include_once('../../../configuracion/conectar.php');
    include_once('../../../configuracion/define_variables.php');
    $id_empresa = $_SESSION['EMPRESA'];

    // consultar las fechas donde hay log
    $sql = "SELECT fecha FROM inventario_totales_log_mensual WHERE id_empresa=$id_empresa AND id_bodega=$id_bodega AND fecha IS NOT NULL GROUP BY fecha ORDER BY fecha DESC";
    $query = $mysql->query($sql);
    while($row = $mysql->fetch_array($query)){
        $dates .= "<option value='$row[fecha]'>$row[fecha]</option>";
    }

?>

<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha" style="width: 300px;">
        <div class="WizTitulo">Asistente Generador excel inventario</div>
        <div  class="WizContenido grid grid-cols-2 gap-4">
            <span class="font-semibold">Fecha</span>
            <span>
                <select id="fecha_inventario" >
                    <option value="">Actual</option>
                    <?=$dates?>
                </select>
            </span>
            <span class="font-semibold" >Separador de decimales</span>
            <span>
                <select id="separadorDecimales" onChange="validarSelect('decimales');" style="width: 130px;">
                    <option value=".">Punto (.)</option>
                    <option value=",">Coma (,)</option>
                </select>
            </span>
            <span class="font-semibold" >Separador de miles</span>
            <span>
                <select id="separadorMiles" onChange="validarSelect('miles');" style="width: 130px;">
                    <option value=",">Coma (,)</option>
                    <option value=".">Punto (.)</option>
                    <option value="">Sin separador</option>
                </select>
            </span>
            <!-- <b>Separador de decimales</b> -->
            <!-- <br /> -->
            <!-- <br /> -->
            <!-- </br> -->
            <!-- </br> -->
            <!-- <b>Separador de miles</b> -->
                <!-- <br /> -->
                <!-- <br /> -->
            <!-- </br> -->
            <!-- </br> -->
            <!-- <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Generar excel>>
            </button> -->
            <button type="button" onClick="generarInventarioExcel();" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                Generar
            </button>

            <!-- <input type="button" value="Generar excel>>" onClick="generarInventarioExcel();" style="width:110px; height:30px;"/> -->
        </div>
    </div>
</div>