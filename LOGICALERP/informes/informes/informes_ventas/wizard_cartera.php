<style>
    /*ESTILOS DEL WIZARD Y DE LA GRILLA ESTAN EN INDEX.CSS, ESTE ESTILO ES PARA PERSONALIZACION DE CONTENIDO*/
    .sub-content[data-position="right"]{width: calc(60% - 3px); }
    .sub-content[data-position="left"]{width: 40%; overflow:auto;}
    .content-grilla-filtro { height: calc(50% - 45px);}
    .content-grilla-filtro .cell[data-col="1"]{width: 22px;}
    .content-grilla-filtro .cell[data-col="2"]{width: 89px;}
    .content-grilla-filtro .cell[data-col="3"]{width: 190px;}
  /*.content-grilla-filtro .cell[data-col="4"]{width: 58px; text-align: right;border-right: none;}*/
    .sub-content [data-width="input"]{width: 150px;}
    .selected_check{float: right;}
</style>

<div class="main-content">
    <div class="sub-content" data-position="right">
        <div class="title">FILTRAR POR CLIENTES</div>

        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Documento</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaTercero();"></div>
                <!-- <div class="cell" data-col="4" data-icon="un_checked" onclick="cambiaCheckERC(this);" id="div_check_terceros"> <span>Todos </span> </div> -->
            </div>
            <div class="body" id="body_grilla_filtro">
            </div>
        </div>

        <div class="title">CUENTAS DE PAGO</div>
        <div class="content-grilla-filtro">
            <div class="head">
                <div class="cell" data-col="1"></div>
                <div class="cell" data-col="2">Cuenta</div>
                <div class="cell" data-col="3">Nombre</div>
                <div class="cell" data-col="1" data-icon="search" title="Buscar Clientes" onclick="ventanaBusquedaCuentasPago();"></div>
            </div>
            <div class="body" id="body_grilla_filtro_cuenta_pago">
            </div>
        </div>

    </div>

    <div class="sub-content" data-position="left">
        <div class="title">FORMATO DE DIGITOS</div>
        <p>
          <table>
            <tbody>
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
            </tbody>
          </table>
        </p>
        <div class="title">EDADES
            <div class="selected_check"  id="div_check_terceros">
                <input onclick="selected_check(this);" type="checkbox" value="" >
                <span>Todos </span>
            </div>
        </div>
        <p>
            <table>
                <tr>
                    <td><input type="checkbox" id="plazo_por_vencer" value="por_vencer" ></td><td>Por vencer</td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="vencido_1_30" value="vencido_1_30" ></td><td>Vencido 1-30 dias</td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="vencido_31_60" value="vencido_31_60" ></td><td>Vencido 31-60 dias</td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="vencido_61_90" value="vencido_61_90" ></td><td>Vencido 61-90 dias</td>
                </tr>
                <tr>
                    <td><input type="checkbox" id="vencido_mas_90" value="vencido_mas_90" ></td><td>Vencido mas de 90 dias</td>
                </tr>
            </table>
        </p>
        <div class="title">AGRUPACION</div>
        <p>
            <select data-width="input" id="agrupacion">
                <option value="Clientes">Clientes</option>
                <option value="Facturas">Facturas</option>
            </select>
        </p>
        <div class="title">TIPO DE INFORME</div>
        <p>
            <select data-width="input" id="tipo_informe">
                <option value="detallado">Detallado</option>
                <option value="totalizado_terceros">Totalizado por Terceros</option>
                <option value="totalizado_edades">Totalizado por Edades</option>
            </select>
        </p>
        <div class="title">FECHAS DEL INFORME</div>
        <p>
            <select data-width="input" id="tipo_fecha_informe" onchange="changeDate()">
                <option value="corte">Con corte a</option>
                <option value="rango_fechas">Rango de fechas</option>
            </select>
            <br>
            <br>
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
        <div class="title">ORDENAR</div>
        <p>
            <select data-width="input" id="order">
                <option value="cliente">Cliente</option>
                <option value="numero_factura">Consecutivo</option>
            </select>
            <br>
            <br>
            <select data-width="input" id="by">
                <option value="ASC">Ascendente</option>
                <option value="DESC">Descendente</option>
            </select>
        </p>
    </div>
</div>
<script>
    var rows = '';

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


    var bool
    // CHECK DE LAS EDADES
    if (typeof(localStorage.plazo_por_vencer)!="undefined")
        if (localStorage.plazo_por_vencer!="")
            if(document.getElementById("plazo_por_vencer"))
                bool = (localStorage.plazo_por_vencer=='true')? true : false ;
                document.getElementById("plazo_por_vencer").checked=bool;


    if (typeof(localStorage.vencido_1_30)!="undefined")
        if (localStorage.vencido_1_30!="")
            if(document.getElementById("vencido_1_30"))
                bool = (localStorage.vencido_1_30=='true')? true : false ;
                document.getElementById("vencido_1_30").checked=bool;

    if (typeof(localStorage.vencido_31_60)!="undefined")
        if (localStorage.vencido_31_60!="")
            if(document.getElementById("vencido_31_60"))
                bool = (localStorage.vencido_31_60=='true')? true : false ;
                document.getElementById("vencido_31_60").checked=bool;


    if (typeof(localStorage.vencido_61_90)!="undefined")
        if (localStorage.vencido_61_90!="")
            if(document.getElementById("vencido_61_90"))
                bool = (localStorage.vencido_61_90=='true')? true : false ;
                document.getElementById("vencido_61_90").checked=bool;

    if (typeof(localStorage.vencido_mas_90)!="undefined")
        if (localStorage.vencido_mas_90!="")
            if(document.getElementById("vencido_mas_90"))
                bool = (localStorage.vencido_mas_90=='true')? true : false ;
                document.getElementById("vencido_mas_90").checked=bool;

    // DEMAS FILTROS
    if (typeof(localStorage.sucursal_cartera_edades)!="undefined")
        if (localStorage.sucursal_cartera_edades!="")
            if(document.getElementById("filtro_sucursal_cartera_edades"))
                document.getElementById("filtro_sucursal_cartera_edades").value=localStorage.sucursal_cartera_edades;

    if (typeof(localStorage.MyInformeFiltroFechaInicioCartera)!="undefined")
        if (localStorage.MyInformeFiltroFechaInicioCartera!="")
            document.getElementById("MyInformeFiltroFechaInicio").value=localStorage.MyInformeFiltroFechaInicioCartera;

    if (typeof(localStorage.MyInformeFiltroFechaFinalCartera)!="undefined")
        if (localStorage.MyInformeFiltroFechaFinalCartera!="")
            document.getElementById("MyInformeFiltroFechaFinal").value=localStorage.MyInformeFiltroFechaFinalCartera;

    if (typeof(localStorage.agrupacion_cartera_cliente)!="undefined")
        if (localStorage.agrupacion_cartera_cliente!="")
            document.getElementById("agrupacion").value=localStorage.agrupacion_cartera_cliente;

    if (typeof(localStorage.tipo_fecha_informe)!="undefined")
        if (localStorage.tipo_fecha_informe!="")
            document.getElementById("tipo_fecha_informe").value=localStorage.tipo_fecha_informe;

    if (typeof(localStorage.tipo_informe_cartera_edades)!="undefined")
        if (localStorage.tipo_informe_cartera_edades!="")
            document.getElementById("tipo_informe").value=localStorage.tipo_informe_cartera_edades;

    if (typeof(localStorage.order_cartera_cliente)!="undefined")
        if (localStorage.order_cartera_cliente!="")
            document.getElementById("order").value=localStorage.order_cartera_cliente;

    if (typeof(localStorage.by_cartera_cliente)!="undefined")
        if (localStorage.by_cartera_cliente!="")
            document.getElementById("by").value=localStorage.by_cartera_cliente;



    if (typeof(localStorage.separador_miles_cartera_cliente)!="undefined")
        if (localStorage.separador_miles_cartera_cliente!="")
            document.getElementById("separador_miles").value=localStorage.separador_miles_cartera_cliente;

    if (typeof(localStorage.separador_decimales_cartera_cliente)!="undefined")
        if (localStorage.separador_decimales_cartera_cliente!="")
            document.getElementById("separador_decimales").value=localStorage.separador_decimales_cartera_cliente;

    // RECORRER EL ARRAY PARA RENDERIZAR LOS TERCEROS DEL FILTRO
    clientesConfigurados.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro').innerHTML=rows;

    rows = '';
    // RECORRER EL ARRAY PARA RENDERIZAR LOS CENTROS DE COSTO DEL FILTRO
    cuentasPagoVentaConfigurados.forEach(function(elemento) {rows += elemento;});
    document.getElementById('body_grilla_filtro_cuenta_pago').innerHTML=rows;

    function changeDate() {
        var tipo_fecha_informe = document.getElementById('tipo_fecha_informe').value;
        // corte
        // rango_fechas
        if (tipo_fecha_informe=='corte'){ document.getElementById('MyInformeFiltroFechaInicio').parentNode.parentNode.parentNode.style.display = "none"; }
        else{ document.getElementById('MyInformeFiltroFechaInicio').parentNode.parentNode.parentNode.style.display = "contents"; }
    }

    changeDate();

    function selected_check(checkbox) {
        plazo_por_vencer = document.getElementById("plazo_por_vencer");
        vencido_1_30     = document.getElementById("vencido_1_30");
        vencido_31_60    = document.getElementById("vencido_31_60");
        vencido_61_90    = document.getElementById("vencido_61_90");
        vencido_mas_90   = document.getElementById("vencido_mas_90");
        if (checkbox.checked == true) {
            plazo_por_vencer.checked = true;
            vencido_1_30.checked     = true;
            vencido_31_60.checked    = true;
            vencido_61_90.checked    = true;
            vencido_mas_90.checked   = true;
        }
        else{
            plazo_por_vencer.checked = false;
            vencido_1_30.checked     = false;
            vencido_31_60.checked    = false;
            vencido_61_90.checked    = false;
            vencido_mas_90.checked   = false;
        }
    }

</script>