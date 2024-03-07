<style>

    .WizContenido {
        margin: 0px;
    }

    .contenedor_tablas {
        float            : left;
        width            : 90%;
        background-color : #FFF;
        margin-top       : 10px;
        margin-left      : 20px;
        border           : 1px solid #D4D4D4;
    }

    .headDivs{
        float            : left;
        background-color : #F3F3F3;
        padding          : 5 0 5 3;
        font-size        : 11px;
        font-weight      : bold;
        border-right     : 1px solid #D4D4D4;
        border-bottom    : 1px solid #D4D4D4;
    }

    .filaDivs {
        float         : left;
        border-right  : 1px solid #D4D4D4;
        padding       : 5 0 5 3;
        overflow      : hidden;
        white-space   : nowrap;
        text-overflow : ellipsis;
    }

    .divIcono {
        float            : left;
        width            : 20px;
        height           : 16px;
        padding          : 3 0 4 5;
        background-color : #F3F3F3;
        overflow         : hidden;
    }

    .divIcono>img {
        cursor : pointer;
        width  : 16px;
        height : 16px;
    }

    input[type=text]{
        border     : none;
        box-shadow : none;
        height     : 30px
    }

    .content-buttom{
        width      : 100%;
        float      : left;
        text-align : center;
        margin-top : 15px;
    }

    input[type=button],button{
        border                : none;
        background-repeat     : no-repeat;
        background-color      : #DFE8F6;
        padding               : 35px 0px 0px 0px;
        background-position-x : center;
        cursor                : pointer;
        box-sizing            : border-box;
        border                : 2px solid #DFE8F6;
    }

    input[type=button]:hover,button:hover{
        border: 2px solid #000;
        background-color: rgba(0,0,0,0.3);
    }

    .generate{
        background-image: url('../../temas/clasico/images/BotonesTabs/genera_informe.png');
    }

    .generatePDF{
        background-image: url('../../temas/clasico/images/BotonesTabs/genera_pdf.png');
    }

    .user_search{
        background-image: url('../../temas/clasico/images/BotonesTabs/user_search.png');
    }

    #WizCapaDerecha{
        padding-top: 10px;
    }

</style>

<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha">
        <div class="WizTitulo">Asistente Generador del Certificado</div>
        <div  class="WizContenido">

            <div class="contenedor_tablas" style="width:105px;">
                <div class="headDivs" style="width:105px;">Fecha Inicial</div>
                <!-- <div class="headDivs" style="width:calc(50% - 4px);border-right:none;">Fecha Final</div> -->
                <div class="filaDivs" style="width:105px;"><input type="text" class="myfield" id="fecha_inicial" /></div>
            </div>
            <div class="contenedor_tablas" style="width:105px;">
                <div class="headDivs" style="width:105px;">Fecha Final</div>
                <!-- <div class="headDivs" style="width:calc(50% - 4px);border-right:none;">Fecha Final</div> -->
                <div class="filaDivs" style="width:105px;"><input type="text" class="myfield" id="fecha_final" /></div>
            </div>

            <div class="contenedor_tablas">
                <div class="headDivs" style="width:130px;">Documento</div>
                <div class="headDivs" style="width:calc(100% - 138px);border-right:none;">Tercero</div>
                <div class="filaDivs" style="width:130px;" id="documento_tercero">&nbsp;</div>
                <div class="filaDivs" style="width:calc(100% - 138px - 25px);" id="nombre_tercero">&nbsp;</div>
                <input type="hidden" id="id_tercero">
                <div class="divIcono" onclick="ventanaBusquedaEmpleados()">
                    <img src="img/buscar20.png" title="Buscar Cuenta">
                </div>
            </div>

            <div class="content-buttom">
                <input type="button" class="generate" value="Generar" onClick="generarHtml();" />
                <input type="button" class="generatePDF" value="Generar" onClick="generar_Excel()" style="margin-left:10px;" />
                <button class="user_search" onClick="ventanaBusquedaTercerosACertificar()">Terceros a<br> Certificar</button>
            </div>

        </div>
    </div>
</div>

<script>

    new Ext.form.DateField({
        format     : 'Y-m-d',               //FORMATO
        width      : 100,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_inicial',
        editable   : false,                 //EDITABLE
        value      : new Date(),             //VALOR POR DEFECTO
    });

    new Ext.form.DateField({
        format     : 'Y-m-d',               //FORMATO
        width      : 100,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_final',
        editable   : false,                 //EDITABLE
        value      : new Date(),             //VALOR POR DEFECTO
    });

    if (typeof(localStorage.fecha_inicio_CRFTE)!="undefined") {
        if (localStorage.fecha_inicio_CRFTE!="") {
            // var fecha = localStorage.fecha_inicio_CRFTE.split('-');
            document.getElementById("fecha_inicial").value=localStorage.fecha_inicio_CRFTE;
        }
    }

    if (typeof(localStorage.fecha_final_CRFTE)!="undefined") {
        if (localStorage.fecha_final_CRFTE!="") {
            // var fecha = localStorage.fecha_final_CRFTE.split('-');
            document.getElementById("fecha_final").value=localStorage.fecha_final_CRFTE;
        }
    }

    if (typeof(localStorage.id_tercero_CRFTE)!="undefined") {
        if (localStorage.id_tercero_CRFTE!="") {
            document.getElementById("id_tercero").value=localStorage.id_tercero_CRFTE;
        }
    }

    if (typeof(localStorage.documento_tercero_CRFTE)!="undefined") {
        if (localStorage.documento_tercero_CRFTE!="") {
            document.getElementById("documento_tercero").innerHTML=localStorage.documento_tercero_CRFTE;
        }
    }

    if (typeof(localStorage.nombre_tercero_CRFTE)!="undefined") {
        if (localStorage.nombre_tercero_CRFTE!="") {
            document.getElementById("nombre_tercero").innerHTML=localStorage.nombre_tercero_CRFTE;
        }
    }





    function ventanaBusquedaEmpleados() {
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_empleado = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_empleado',
            title       : 'Buscar Empleado',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFuncion  : 'renderTerceroCertificado(id);',
                    nombre_grilla : 'TerceroCertificado'
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); Win_Ventana_buscar_empleado.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function renderTerceroCertificado(id){
        var documento = document.getElementById('div_TerceroCertificado_numero_identificacion_'+id).innerHTML;
        var nombre    = document.getElementById('div_TerceroCertificado_nombre_'+id).innerHTML;

        document.getElementById('documento_tercero').innerHTML = documento;
        document.getElementById('nombre_tercero').innerHTML    = nombre;
        document.getElementById('id_tercero').value            = id;

        Win_Ventana_buscar_empleado.close(id)

    }

    function ventanaBusquedaTercerosACertificar(){
        var fecha_inicial = document.getElementById('fecha_inicial').value
        ,   fecha_final   = document.getElementById('fecha_final').value;

        Win_Ventana_buscar_empleado = new Ext.Window({
            width       : 450,
            height      : 400,
            id          : 'Win_Ventana_buscar_empleado',
            title       : 'Terceros a Certificar',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'informes/tributario/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    fecha_inicial : fecha_inicial,
                    fecha_final   : fecha_final,
                    opc           : 'ventana_busqueda_terceros_certificar',
                    informe       : 'Retefte',
                }
            },
            tbar        :
            [
                {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    style   : 'border-right:none;',
                    items   :
                    [
                        {
                            xtype       : 'button',
                            width       : 60,
                            height      : 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'top',
                            hidden      : false,
                            handler     : function(){ BloqBtn(this); Win_Ventana_buscar_empleado.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

</script>