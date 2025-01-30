<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha" style="width: 300px;">
        <div class="WizTitulo">Asistente Generador Orden de compra</div>
        <div  class="WizContenido grid grid-cols-2 gap-4">
            <span class="font-semibold" >Separador de decimales</span>
            <span>
                <select id="separadorDecimalesOC" onChange="validarSelectOC('decimales');" style="width: 130px;">
                    <option value=".">Punto (.)</option>
                    <option value=",">Coma (,)</option>
                </select>
            </span>
            <span class="font-semibold" >Separador de miles</span>
            <span>
                <select id="separadorMilesOC" onChange="validarSelectOC('miles');" style="width: 130px;">
                    <option value=",">Coma (,)</option>
                    <option value=".">Punto (.)</option>
                    <option value="">Sin separador</option>
                </select>
            </span>
            <button type="button" onClick="imprimirOrdenCompraExcel();" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">
                Generar
            </button>
        </div>
    </div>
</div>