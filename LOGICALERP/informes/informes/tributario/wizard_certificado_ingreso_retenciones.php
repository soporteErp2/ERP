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

    input[type=button]{
        border                : none;
        background-repeat     : no-repeat;
        background-color      : #DFE8F6;
        padding               : 35px 0px 0px 0px;
        background-position-x : center;
        cursor                : pointer;
        box-sizing            : border-box;
        border                : 2px solid #DFE8F6;
    }

    input[type=button]:hover{
        border: 2px solid #000;
        background-color: rgba(0,0,0,0.3);
    }

    .generate{
        background-image: url('../../temas/clasico/images/BotonesTabs/genera_informe.png');
    }

    .generateExcel{
        background-image: url('../../temas/clasico/images/BotonesTabs/excel32.png');
    }

</style>

<div id="WizCapaPrincipal">
    <div id="WizCapaIzquierda"></div>
    <div id="WizCapaDerecha">
        <div class="WizTitulo">Asistente Generador del Certificado</div>
        <div  class="WizContenido">

            <div class="contenedor_tablas">
                <div class="headDivs" style="width:calc(50% - 4px);">Fecha Inicial</div>
                <div class="headDivs" style="width:calc(50% - 4px);border-right:none;">Fecha Final</div>
                <div class="filaDivs" style="width:calc(50% - 4px);"><input type="text" class="myfield" id="fecha_inicial" /></div>
                <div class="filaDivs" style="width:calc(50% - 4px);border-right:none;"><input type="text" class="myfield" id="fecha_final" /></div>
            </div>

            <div class="contenedor_tablas">
                <div class="headDivs" style="width:130px;">Documento</div>
                <div class="headDivs" style="width:calc(100% - 138px);border-right:none;">Empleado</div>
                <div class="filaDivs" style="width:130px;" id="documento_empleado">&nbsp;</div>
                <div class="filaDivs" style="width:calc(100% - 138px - 25px);" id="nombre_empleado">&nbsp;</div>
                <input type="hidden" id="id_empleado">
                <div class="divIcono" onclick="ventanaBusquedaEmpleados()">
                    <img src="img/buscar20.png" title="Buscar Cuenta">
                </div>
            </div>

            <div class="content-buttom">
                <input type="button" class="generate" value="Generar" onClick="generarHtml();" />
                <input type="button" class="generateExcel" value="Generar" onClick="generar_Excel()" style="margin-left:10px;" />
            </div>

        </div>
    </div>
</div>

<script>

    new Ext.form.DateField({
        format     : 'Y-m-d',               //FORMATO
        width      : 175,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_inicial',
        editable   : false,                 //EDITABLE
        value      : new Date(),             //VALOR POR DEFECTO
    });

    new Ext.form.DateField({
        format     : 'Y-m-d',               //FORMATO
        width      : 175,                   //ANCHO
        allowBlank : false,
        showToday  : false,
        applyTo    : 'fecha_final',
        editable   : false,                 //EDITABLE
        value      : new Date(),             //VALOR POR DEFECTO
    });

    if (typeof(localStorage.fecha_inicio_CIRE)!="undefined") {
                if (localStorage.fecha_inicio_CIRE!="") {
                    document.getElementById("fecha_inicial").value=localStorage.fecha_inicio_CIRE;
                }
            }
    if (typeof(localStorage.fecha_final_CIRE)!="undefined") {
        if (localStorage.fecha_final_CIRE!="") {
            document.getElementById("fecha_final").value=localStorage.fecha_final_CIRE;
        }
    }
    if (typeof(localStorage.id_empleado_CIRE)!="undefined") {
        if (localStorage.id_empleado_CIRE!="") {
            document.getElementById("id_empleado").value=localStorage.id_empleado_CIRE;
        }
    }
    if (typeof(localStorage.documento_empleado_CIRE)!="undefined") {
        if (localStorage.documento_empleado_CIRE!="") {
            document.getElementById("documento_empleado").innerHTML=localStorage.documento_empleado_CIRE;
        }
    }
    if (typeof(localStorage.nombre_empleado_CIRE)!="undefined") {
        if (localStorage.nombre_empleado_CIRE!="") {
            document.getElementById("nombre_empleado").innerHTML=localStorage.nombre_empleado_CIRE;
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
                url     : '../funciones_globales/grillas/BusquedaEmpleados.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFuncion  : 'renderEmpleadoCertificado(id);',
                    nombre_grilla : 'empleadoCertificado'
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

    function renderEmpleadoCertificado(id){
        var documento = document.getElementById('div_empleadoCertificado_documento_'+id).innerHTML;
        var nombre    = document.getElementById('div_empleadoCertificado_nombre_'+id).innerHTML;

        document.getElementById('documento_empleado').innerHTML = documento;
        document.getElementById('nombre_empleado').innerHTML    = nombre;
        document.getElementById('id_empleado').value            = id;

        Win_Ventana_buscar_empleado.close(id)

    }

</script>