<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(64% - 2px); }
    .sub-content[data-position="left"]{width: 36%;}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR TERCEROS</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTerceroFVG()"></div>
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">FRECUENCIA DE LA GRAFICA</div>
            <p>
                <select id="tipo_informe" style="width:150px">
                    <option value="dias">Por Dias</option>
                    <option value="meses">Por meses</option>
                    <!-- <option value="anios">Por Años</option> -->
                </select>
            </p>
        <div class="title">VALORES DE LA GRAFICA</div>
            <p>
                <select id="valores_informe" style="width:150px">
                    <option value="sin_iva">Subtotal (Sin Iva)</option>
                    <option value="con_iva">Total (Con Iva)</option>
                </select>
            </p>
            <!-- <p>
                <input type="checkbox"> Mostrar Valores en puntos
            </p> -->
        <div class="title">RANGO DE FECHAS</div>
        <p>
            <table>
                <tr>
                    <td>Fecha Inicial</td>
                    <td><input type="text" id="fecha_inicial"/></td>
                </tr>
                <tr>
                    <td>Fecha Final</td>
                    <td><input type="text" id="fecha_final"/></td>
                </tr>
            </table>
        </p>
    </div>
</div>
<script>

    var contTercero = 1
    ,   rows        = '';

    // FILTRO PARA DIAS
    new Ext.form.DateField({
        format     : 'Y-m-d',
        width      : 100,
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_inicial',
        editable   : false,
        value      : '2016-10-01',
    });
    new Ext.form.DateField({
        format     : 'Y-m-d',
        width      : 100,
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_final',
        editable   : false,
        value      : '2016-10-20',
    });

    // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    tercerosConfiguradosFVG.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    if (typeof(localStorage.MyInformeFiltroTipoGraficaFV)!="undefined")
        if (localStorage.MyInformeFiltroTipoGraficaFV!="")
            document.getElementById("tipo_informe").value=localStorage.MyInformeFiltroTipoGraficaFV;

    if (typeof(localStorage.MyInformeFiltroValoresGraficaFV)!="undefined")
        if (localStorage.MyInformeFiltroValoresGraficaFV!="")
            document.getElementById("valores_informe").value=localStorage.MyInformeFiltroValoresGraficaFV;

    if (typeof(localStorage.MyInformeFiltroFechaInicial)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicial!="")
            document.getElementById("fecha_inicial").value=localStorage.MyInformeFiltroFechaInicial;

    if (typeof(localStorage.MyInformeFiltroFechaFinal)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinal!="")
            document.getElementById("fecha_final").value=localStorage.MyInformeFiltroFechaFinal;


    function generaFormato(opc){
        var tipo_informe      = document.getElementById('tipo_informe').value
        ,   valores_informe   = document.getElementById('valores_informe').value
        ,   fecha_inicial     = document.getElementById('fecha_inicial').value
        ,   fecha_final       = document.getElementById('fecha_final').value
        ,   arraytercerosJSON = Array()
        ,   i                 = 0
        ,   filtroSucursal    = document.getElementById('filtro_sucursal_facturadGraficos').value

        arraytercerosFVG.forEach(function(id_tercero) {  arraytercerosJSON[i] = id_tercero; i++;  });
        arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        Ext.get('RecibidorInforme_facturas_venta').load({
            url     : '../informes/informes/graficos/informe_facturas_venta_Result.php',
            text    : 'Generando Informe...',
            scripts : true,
            nocache : true,
            params  :
            {
                filtroSucursal    : filtroSucursal,
                tipo_informe      : tipo_informe,
                valores_informe   : valores_informe,
                fecha_inicial     : fecha_inicial,
                fecha_final       : fecha_final,
                arraytercerosJSON : arraytercerosJSON,
            }
        });

        localStorage.MyInformeFiltroSucursalFVG      = filtroSucursal;
        localStorage.MyInformeFiltroTipoGraficaFV    = tipo_informe;
        localStorage.MyInformeFiltroValoresGraficaFV = valores_informe;
        localStorage.MyInformeFiltroFechaInicial     = fecha_inicial;
        localStorage.MyInformeFiltroFechaFinal       = fecha_final;
    }

    function ventanaBusquedaTerceroFVG(){
        tabla='terceros';
        tercero='nombre_comercial';
        titulo_ventana='Clientes';

        Win_VentanaCliente_tercerosFVG = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_tercerosFVG',
            title       : titulo_ventana,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    tabla                : tabla,
                    id_tercero           : 'id',
                    tercero              : tercero,
                    opcGrillaContable    : 'facturadGraficos',
                    cargaFuncion         : '',
                    nombre_grilla        : '',
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
                    handler     : function(){ Win_VentanaCliente_tercerosFVG.close(id) }
                }
            ]
        }).show();
    }

    function checkGrilla(checkbox,cont,tabla){

        if (checkbox.checked ==true) {
            var div   = document.createElement('div');
            div.setAttribute('id','row_tercero_FVG_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_tercero_FVG_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaClienteFV(${cont})" title="Eliminar Cliente"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosFVG[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_FVG_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosFVG[cont]=checkbox.value;

        }
        else if (checkbox.checked ==false) {
            delete arraytercerosFVG[cont];
            delete tercerosConfiguradosFVG[cont];
            (document.getElementById("row_tercero_FVG_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_FVG_"+cont));

        }

    }

    function eliminaClienteFV(cont){
        delete arraytercerosFVG[cont];
        delete tercerosConfiguradosFVG[cont];
        (document.getElementById("row_tercero_FVG_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_FVG_"+cont));
    }

</script>