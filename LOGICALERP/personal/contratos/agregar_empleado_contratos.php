<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../../../misc/MyGrilla/class.MyGrilla.php");

    /**//////////////////////////////////////////////**/
    /**///       INICIALIZACION DE LA CLASE       ///**/
    /**/                                            /**/
    /**/    $grilla = new MyGrilla();               /**/
    /**/                                            /**/
    /**//////////////////////////////////////////////**/

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_pais     = $_SESSION['PAIS'];

    echo '<script>var contratoActivo =  "false"</script>';//con esta variable controlo si tiene contrato activo o no

    //CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

        //NOMBRE DE LA GRILLA
            $grilla->GrillaName         = 'empleados_contratos';   //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
        //QUERY
            $grilla->TableName          = 'empleados_contratos';            //NOMBRE DE LA TABLA EN LA BASE DE DATOS
            $grilla->MyWhere            = "activo = 1 AND id_empresa = '$id_empresa' AND id_empleado=$ID";      //WHERE DE LA CONSULTA A LA TABLA "$TableName"
            $grilla->MySqlLimit         = '0,50';           //LIMITE DE LA CONSULTA

        //TAMANO DE LA GRILLA
            $grilla->AutoResize       = 'true';           //SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            // $grilla->Ancho              = 610;              //ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            // $grilla->Alto               = 360;              //ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
            $grilla->QuitarAncho      = 70;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
            $grilla->QuitarAlto       = 250;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

        //TOOLBAR Y CAMPO DE BUSQUEDA
            $grilla->Gtoolbar           = 'true';           //SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
            $grilla->CamposBusqueda     = 'fecha_inicio_contrato,fecha_fin_contrato,tipo_contrato';     //VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

        //CONFIGURACION DE CAMPOS EN LA GRILLA
            // $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
            $grilla->AddRow('Fecha Inicial','fecha_inicio_contrato',120);
            $grilla->AddRow('Fecha Final','fecha_fin_contrato',120);
            $grilla->AddRow('Tipo Contrato','tipo_contrato',250);
            // $grilla->AddRow('Sucursal','sucursal',250);

            $grilla->AddRowImage('Entidades','<center><img src="images/entidades.png" style="cursor:pointer;" title="Entidades del Empleado" onclick="ventanaEntidadesEmpleado([id])"></center>',65);
            $grilla->AddRowImage('Estado','<center><img src="images/[estado].png" id="estado_[id]" data-value="[estado]"></center><input type="hidden" value="[estado]" id="estado_[id]"><script>if(\'[estado]\'== 0){contratoActivo = "true"}</script>',65);
            $grilla->AddRowImage('Venc. Firm','<center><img src="images/contrato_vencimiento_[vencimiento_firmado].png" style="cursor:pointer" width="16" height="16" id="img_contrato_vencimiento_[id]" onclick="ventanaDocumentosContratos([id]);"/></center>','60');

        //CONFIGURACION FORMULARIO
            $grilla->FContenedorAncho       = 300;
            $grilla->FColumnaGeneralAncho   = 290;
            $grilla->FColumnaGeneralAlto    = 25;
            $grilla->FColumnaLabelAncho     = 120;
            $grilla->FColumnaFieldAncho     = 200;

        //CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
            $grilla->VentanaAuto        = 'false';           //SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
            $grilla->TituloVentana      = 'Nuevo Contrato'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
            $grilla->VBarraBotones      = 'true';           //SI HAY O NO BARRA DE BOTONES
            $grilla->VBotonNuevo        = 'true';           //SI LLEVA EL BOTON DE AGREGAR REGISTRO
            $grilla->VBotonNText        = 'Nuevo Contrato'; //TEXTO DEL BOTON DE NUEVO REGISTRO
            $grilla->VBotonNImage       = 'contrato1';            //IMAGEN CSS DEL BOTON
            // $grilla->AddBotton('Estructura','sucursal','VentanaEstructuraEmpresa();');
            $grilla->VAutoResize        = 'true';           //SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
            $grilla->VAncho             = 380;              //ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VAlto              = 400;              //ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
            $grilla->VQuitarAncho       = 70;               //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VQuitarAlto        = 160;              //AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
            $grilla->VAutoScroll        = 'false';          //SI LA VENTANA TIENE O NO AUTOSCROLL
            $grilla->VBotonEliminar     = 'true';           //SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
            $grilla->VComporEliminar    = 'true';           //COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

            //CONFIGURACION DEL MENU CONTEXTUAL
            $grilla->MenuContext        = 'true';       //MENU CONTEXTUAL
            $grilla->MenuContextEliminar= 'false';

            //OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
            $grilla->AddMenuContext('Terminar/Cancelar Contrato','doc_cancel','ventana_cancelar_contrato([id])');

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**//////////////////////////////////////////////////////////////**/
    /**///              INICIALIZACION DE LA GRILLA               ///**/
    /**/                                                            /**/
    /**/    $grilla->Link = $link;      //Conexion a la BD          /**/
    /**/    $grilla->inicializa($_POST);//variables POST            /**/
    /**/    $grilla->GeneraGrilla();    // Inicializa la Grilla     /**/
    /**/                                                            /**/
    /**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){ ?>

    <script>

        function Agregar_empleados_contratos() {
            valida_cantidad_contrato();
            // ventanaAgregarActualizarContrato();
        }


        function Editar_empleados_contratos(id){
            ventanaAgregarActualizarContrato(id);
        }

        function ventanaAgregarActualizarContrato(id) {
            var title    = 'Agregar Contrato'
            ,   text_btn = 'Guardar'
            ,   estado   = 0
            if (id>0) {
                title    = 'Actualizar Contrato';
                text_btn = 'Actualizar';
                estado   = document.getElementById('estado_'+id).dataset.value
            }

            Win_Ventana_agregar_contrato = new Ext.Window({
                width       : 450,
                height      : 500,
                id          : 'Win_Ventana_agregar_contrato',
                title       : title,
                modal       : true,
                autoScroll  : false,
                closable    : true,
                autoDestroy : true,
                bodyStyle       : 'background-color:#FFF;',
                autoLoad    :
                {
                    url     : 'contratos/form_contrato.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id_empleado : '<?php echo $ID; ?>',
                        id_contrato : id,
                        estado      : estado,
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        //width   : 400,
                        title   : 'Opciones',
                        items   :
                        [
                            {
                                xtype     : 'button',
                                width     : 60,
                                height    : 56,
                                id        : 'btn_contrato',
                                text      : text_btn,
                                scale     : 'large',
                                iconCls   : 'guardar',
                                iconAlign : 'top',
                                handler   : function(){ guardar_actualizar_contrato(id) }
                            },
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                id          : 'btn_duplicar_contrato',
                                text        : 'Duplicar',
                                scale       : 'large',
                                hidden      : 'true',
                                iconCls     : 'Copier',
                                iconAlign   : 'top',
                                handler     : function(){ BloqBtn(this);duplicar_contrato(id); }
                            },
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                id          : 'btn_regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'top',
                                handler     : function(){ Win_Ventana_agregar_contrato.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function valida_cantidad_contrato(){
            Ext.Ajax.request({
                url     : 'contratos/bd/bd.php',
                params  :
                {
                    opc         : 'valida_cantidad_contrato',
                    id_empleado : '<?php echo $ID; ?>',
                },
                success :function (result, request){
                            if(result.responseText == 'true'){
                                alert('El empleado solo puede tener un contrato activo');
                            }
                            else{ ventanaAgregarActualizarContrato(); }
                        },
                failure : function(){ alert('Sin Conexion'); }
            });
        }

        function guardar_actualizar_contrato(id){
            var numero_contrato         = document.getElementById('numero_contrato').value
            ,   fecha_inicio_contrato   = document.getElementById('fecha_inicio_contrato').value
            ,   fecha_fin_contrato      = document.getElementById('fecha_fin_contrato').value
            ,   salario_basico          = document.getElementById('salario_basico').value
            ,   salario_integral        = document.getElementById('salario_integral').value
            ,   fecha_inicio_nomina     = document.getElementById('fecha_inicio_nomina').value
            ,   id_tipo_contrato        = document.getElementById('id_tipo_contrato').value
            ,   numero_cuenta_bancaria  = document.getElementById('numero_cuenta_bancaria').value
            ,   id_centro_costo         = document.getElementById('centro_costo').dataset.value
            ,   id_grupo_trabajo        = document.getElementById('id_grupo_trabajo').value
            ,   id_nivel_riesgo_laboral = document.getElementById('id_nivel_riesgo_laboral').value
            ,   id_cargo                = document.getElementById('id_cargo').value
            ,   id_tipo_trabajador      = document.getElementById('id_tipo_trabajador').value
            ,   id_subtipo_trabajador   = document.getElementById('id_subtipo_trabajador').value
            ,   id_forma_pago           = document.getElementById('id_forma_pago').value 
            ,   id_medio_pago           = document.getElementById('id_medio_pago').value 
            ,   nombre_banco            = document.getElementById('nombre_banco').value
            ,   tipo_cuenta_bancaria    = document.getElementById('tipo_cuenta_bancaria').value
            ,   opc                     = (id>0)? 'editarContrato' : 'agregarContrato' ;


            var dias = id_tipo_contrato.split(',')[1];
            // if (dias >0 && fecha_fin_contrato =='') {
            //     alert("Aviso\nFaltan algunos campos obligatorios");
            //     return;
            // }

            if (numero_contrato=='' || fecha_inicio_contrato ==''  || salario_basico =='' || fecha_inicio_nomina=='' || id_centro_costo=='' || id_nivel_riesgo_laboral=='' || id_cargo=='') {
                alert("Aviso\nFaltan algunos campos obligatorios");
                console.log('numero_contrato'+numero_contrato+'\nfecha_inicio_contrato'+fecha_inicio_contrato+'\nfecha_fin_contrato'+fecha_fin_contrato+'\nsalario_basico'+salario_basico+'\nfecha_inicio_nomina'+fecha_inicio_nomina+'\nid_centro_costo'+id_centro_costo+'\nid_nivel_riesgo_laboral'+id_nivel_riesgo_laboral);
                return;
            }

            Ext.get('divLoadContrato').load({
                url     : 'contratos/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                     : opc,
                    numero_contrato         : numero_contrato,
                    fecha_inicio_contrato   : fecha_inicio_contrato,
                    fecha_fin_contrato      : fecha_fin_contrato,
                    salario_basico          : salario_basico,
                    salario_integral        : salario_integral,
                    fecha_inicio_nomina     : fecha_inicio_nomina,
                    id_tipo_contrato        : id_tipo_contrato,
                    numero_cuenta_bancaria  : numero_cuenta_bancaria,
                    id_grupo_trabajo        : id_grupo_trabajo,
                    id_nivel_riesgo_laboral : id_nivel_riesgo_laboral,
                    id_tipo_trabajador      : id_tipo_trabajador,
                    id_subtipo_trabajador   : id_subtipo_trabajador,
                    id_forma_pago           : id_forma_pago,
                    id_medio_pago           : id_medio_pago,
                    nombre_banco            : nombre_banco,
                    tipo_cuenta_bancaria    : tipo_cuenta_bancaria,
                    id_empleado             : '<?php echo $ID; ?>',
                    id_centro_costo         : id_centro_costo,
                    id                      : id,
                    sucursal                : '<?php echo $sucursal ?>',
                    id_cargo                : id_cargo,
                }
            });
        }

        function ventana_cancelar_contrato(id) {
            var estado =document.getElementById('estado_'+id).value;
            if (estado==1) { alert("El contrato ya ha sido cancelado/terminado!");return;}
            Win_Ventana_cancelar_contrato = new Ext.Window({
                width       : 360,
                height      : 330,
                id          : 'Win_Ventana_cancelar_contrato',
                title       : 'Cancelar contrato',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc         : 'ventana_cancelar_contrato',
                        id          : id,
                        id_empleado : '<?php echo $ID; ?>',
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Cancelar Contrato',
                                scale       : 'large',
                                iconCls     : 'eliminaruser',
                                iconAlign   : 'top',
                                handler     : function(){ cancelarContrato(id) }
                            },
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'top',
                                handler     : function(){ Win_Ventana_cancelar_contrato.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function ver_historial_salarios(id_empleado,id_contrato) {

            Win_Ventana_historico_salario = new Ext.Window({
                width       : 450,
                height      : 300,
                id          : 'Win_Ventana_historico_salario',
                title       : '',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/ver_historial_salarios.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id_empleado : id_empleado,
                        id_contrato : id_contrato,
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
                                handler     : function(){ BloqBtn(this); Win_Ventana_historico_salario.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function ver_historial_vencimientos(id_contrato) {

            Win_Ventana_historico_vencimiento = new Ext.Window({
                width       : 450,
                height      : 300,
                id          : 'Win_Ventana_historico_vencimiento',
                title       : '',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/ver_historial_vencimientos.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {                        
                        id_contrato : id_contrato,
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
                                handler     : function(){ BloqBtn(this); Win_Ventana_historico_vencimiento.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function cancelarContrato (id) {
            var estado =document.getElementById('estado_'+id).value;
            if (estado==1) { alert("El contrato ya ha sido cancelado/terminado!");return;}
            var fecha_cancelacion       = document.getElementById('fecha_cancelacion').value;
            var id_motivo_fin_contrato  = document.getElementById('id_motivo_fin_contrato').value;
            var motivo_fin_contrato     = document.getElementById('id_motivo_fin_contrato').options[document.getElementById('id_motivo_fin_contrato').selectedIndex].text;
            var observacion_cancelacion = document.getElementById('observacion_cancelacion').value;
            observacion_cancelacion     = observacion_cancelacion.replace(/[\#\<\>\'\"]/g, '');

            if (fecha_cancelacion=='' || id_motivo_fin_contrato=='') {alert('Faltan algunos campos oblogatorios!'); return;}

            Ext.get('divLoadContrato').load({
                url     : 'contratos/bd/bd.php',
                scripts : true,
                nocache : true,
                text : 'Cancelando Contrato...',
                params  :
                {
                    opc                     : 'cancelarContrato',
                    id                      : id,
                    id_empleado             : '<?php echo $ID; ?>',
                    fecha_cancelacion       : fecha_cancelacion,
                    observacion_cancelacion : observacion_cancelacion,
                    id_motivo_fin_contrato  : id_motivo_fin_contrato,
                    motivo_fin_contrato     : motivo_fin_contrato,
                }
            });
        }

        //VENTANA PARA BUSCAR UN CENTRO DE COSTOS
        function ventanaBuscarCentroCostos(){

            Win_Ventana_buscar_centro_costos = new Ext.Window({
                width       : 500,
                height      : 500,
                id          : 'Win_Ventana_buscar_centro_costos',
                title       : 'Seleccionar Centro de costos',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/bd/busca_centro_costos.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        var1 : 'var1',
                        var2 : 'var2',
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'left',
                                handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        //VENTANA PARA ADMINISTRAR LOS CONCEPTOS DE LOS EMPLEADOS EJ: ARL,EPS, ETC
        function ventanaEntidadesEmpleado(id) {
            var estado =document.getElementById('estado_'+id).value;

            Win_Ventana_ventana_entdades = new Ext.Window({
                width       : 700,
                height      : 400,
                id          : 'Win_Ventana_ventana_entdades',
                title       : 'Entidades del Empleado',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/entidades.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc         : 'ventanaEntidadesEmpleado',
                        id_empleado : '<?php echo $ID; ?>',
                        id_contrato : id,
                        estado      : estado,
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Ver Traslados',
                                scale       : 'large',
                                iconCls     : 'doc_traslado',
                                iconAlign   : 'top',
                                handler     : function(){ verTraslados(id); }
                            },
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'top',
                                handler     : function(){ Win_Ventana_ventana_entdades.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        //VENTANA PARA BUSCAR LA ENTIDAD
        function ventanaBuscarEntidad(cont){
            var myalto  = Ext.getBody().getHeight();
            var myancho = Ext.getBody().getWidth();

            Win_Ventana_ventana_buscar_entidad = new Ext.Window({
                width       : myancho-100,
                height      : myalto-50,
                id          : 'Win_Ventana_ventana_buscar_entidad',
                title       : 'Terceros',
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
                        cargaFuncion : 'responseVentanaBuscarEntidad(id,"'+cont+'")',
                        nombre_grilla : 'entidades',
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   :
                        [

                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'left',
                                handler     : function(){ Win_Ventana_ventana_buscar_entidad.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function responseVentanaBuscarEntidad(id,cont) {
            var nombre = document.getElementById('div_entidades_nombre_'+id).innerHTML;
            document.getElementById('id_entidad_'+cont).value=id;
            document.getElementById('entidad_'+cont).innerHTML=nombre;
            Win_Ventana_ventana_buscar_entidad.close();
        }

        //VENTANA PARA BUSCAR EL CONCEPTO
        function ventanaBuscarConcepto(cont,id_contrato){

            Win_Ventana_ventana_buscar_concepto = new Ext.Window({
                width       : 500,
                height      : 400,
                id          : 'Win_Ventana_ventana_buscar_concepto',
                title       : 'Conceptos',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/bd/buscar_concepto.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        cargaFuncion : 'responseVentanaBuscarConcepto(id,"'+cont+'")',
                        id_empleado : '<?php echo $ID; ?>',
                        id_contrato : id_contrato,
                    }
                },
                tbar        :
                [
                    {
                        xtype   : 'buttongroup',
                        columns : 3,
                        title   : 'Opciones',
                        items   :
                        [
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'left',
                                handler     : function(){ Win_Ventana_ventana_buscar_concepto.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        function responseVentanaBuscarConcepto(id,cont) {
            var nombre = document.getElementById('div_nomina_conceptos_descripcion_'+id).innerHTML;
            document.getElementById('id_concepto_'+cont).value=id;
            document.getElementById('concepto_'+cont).innerHTML=nombre;
            Win_Ventana_ventana_buscar_concepto.close();
        }

        function verTraslados(id_contrato){

            Win_Ventana_verTraslados = new Ext.Window({
                width       : 550,
                height      : 450,
                id          : 'Win_Ventana_verTraslados',
                title       : 'Historial de Traslados',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/ver_entidades.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id_contrato : id_contrato,
                        id_empleado : '<?php echo $ID; ?>',
                        // var2 : 'var2',
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
                                handler     : function(){ BloqBtn(this); Win_Ventana_verTraslados.close(id) }
                            }
                        ]
                    }
                ]
            }).show();
        }

        //duplicar contrato
        function duplicar_contrato(id){
            if(confirm("Desea duplicar la informacion del contrato seleccionado")){
                Ext.get('divLoadContrato').load({
                    url     : 'contratos/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc         : 'duplicar_contrato',
                        id_contrato : id,
                    }
                });
            }
        }

            function ventanaDocumentosContratos(id) {

                Win_ventanaContratosVencimientos = new Ext.Window({
                    width       : 550,
                    height      : 500,
                    id          : 'Win_ventanaContratosVencimientos',
                    title       : '',
                    modal       : true,
                    autoScroll  : false,
                    closable    : false,
                    autoDestroy : true,
                    autoLoad    :
                    {
                        url     : 'contratos_vencimientos/documentos_adjuntos.php',
                        scripts : true,
                        nocache : true,
                        params  :
                        {
                            id_contrato   : id,
                        }
                    },
                }).show();

            }

    </script>
<?php
}
else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>
    <script>



    </script>

<?php } ?>
