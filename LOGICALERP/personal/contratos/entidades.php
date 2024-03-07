<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa=$_SESSION['EMPRESA'];

    $sql="SELECT id_entidad,entidad,id_concepto,concepto FROM empleados_contratos_entidades WHERE activo=1 AND id_empresa=$id_empresa AND id_empleado=$id_empleado AND id_contrato=$id_contrato";
        $query=mysql_query($sql,$link);
        $body='';
        $cont=1;
        while ($row=mysql_fetch_array($query)) {
            $btnEliminar=($estado==0)? '<div style="float:right; min-width:70px;padding-top: 0px !important;">

                                            <div onclick="eliminarEntidadConcepto('.$cont.')" id="delete_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px;cursor:pointer;">
                                                <img src="images/delete.png">
                                            </div>
                                            <div onclick="ventana_trasladar_concepto('.$row['id_concepto'].','.$row['id_entidad'].','.$cont.')" id="traslate_'.$cont.'" title="Traslado" style="width:20px; float:left; margin-top:3px;margin-left: 8px;cursor:pointer;">
                                                <img src="images/traslado.png">
                                            </div>

                                        </div>' : '' ;
            $body.='<div class="filaBoleta" id="fila_boleta_'.$cont.'">
                        <div class="campo0" id="loadFila_'.$cont.'">'.$cont.'</div>
                        <div class="campo2" id="entidad_'.$cont.'" title="'.$row['entidad'].'">'.$row['entidad'].'</div>
                        <div class="campo2" id="concepto_'.$cont.'" title="'.$row['concepto'].'">'.$row['concepto'].'</div>
                        '.$btnEliminar.'
                        <input type="hidden" id="id_entidad_'.$cont.'" value="'.$row['id_entidad'].'">
                        <input type="hidden" id="id_concepto_'.$cont.'" value="'.$row['id_concepto'].'">
                    </div>';
            $cont++;
        }
        if ($estado==0) {
            $body.='<div class="filaBoleta" id="fila_boleta_'.$cont.'">
                        <div class="campo0" id="loadFila_'.$cont.'">'.$cont.'</div>
                        <div class="campo1" id="entidad_'.$cont.'"></div>
                        <div class="campoImg" id="divImageBuscarEntidad_'.$cont.'" title="Buscar Entidad"><img src="images/buscar20.png" onclick="ventanaBuscarEntidad(\''.$cont.'\','.$id_contrato.')"></div>
                        <div class="campo1" id="concepto_'.$cont.'"></div>
                        <div class="campoImg" id="divImageBuscarConcepto_'.$cont.'" title="Buscar Concepto"><img src="images/buscar20.png" onclick="ventanaBuscarConcepto(\''.$cont.'\','.$id_contrato.')"></div>
                        <div style="float:right; min-width:70px;padding-top: 0px !important;">
                            <div onclick="guardarEntidadConcepto('.$cont.')" id="divImageSave_'.$cont.'" title="Guardar" style="width:20px; float:left; margin-top:3px;cursor:pointer;"><img src="images/save_true.png" ></div>
                            <div onclick="eliminarEntidadConcepto('.$cont.')" id="delete_'.$cont.'" title="Eliminar Registro" style="width:20px; float:left; margin-top:3px; display:none;cursor:pointer;"><img src="images/delete.png"></div>

                            <div onclick="ventana_trasladar_concepto('.$row['id_concepto'].','.$row['id_entidad'].','.$cont.')" id="traslate_'.$cont.'" title="Traslado" style="width:20px; float:left; margin-top:3px;margin-left: 8px;cursor:pointer; display:none;">
                                <img src="images/traslado.png">
                            </div>

                        </div>

                        <input type="hidden" id="id_entidad_'.$cont.'" value="0">
                        <input type="hidden" id="id_concepto_'.$cont.'" value="0">
                    </div>';
        }

 ?>
<style>
    #contenedor_formulario{
        overflow   : hidden;
        width      : calc(100% - 30px);
        height     : calc(100% - 10px);
        margin     : 15px;
        margin-top : 0px;
    }
    #contenedor_tabla_boletas{
        overflow              : hidden;
        width                 : calc(100% - 2px);
        height                : calc(100% - 5px);
        /*border              : 1px solid #d4d4d4;*/
        border                : 1px solid #D4D4D4   ;
        border-radius         : 4px;
        -webkit-border-radius : 4px;
        -webkit-box-shadow    : 1px 1px 1px #d4d4d4;
        -moz-box-shadow       : 1px 1px 1px #d4d4d4;
        box-shadow            : 1px 1px 1px #d4d4d4;
        background-color      :#F3F3F3;
    }
    .campoImg{
        float            : left;
        width            : 22px;
        border-right     : 1px solid #d4d4d4;
        background-color :#F3F3F3;
        padding-top      : 1px !important;
        height           : 22px !important;
        cursor           : hand;
    }
    .campo0{
        float            : left;
        width            : 26px;
        text-indent      : 5px;
        border-right     : 1px solid #d4d4d4;
        background-color:#F3F3F3;
        padding-top      : 0px !important;
        height: 22px !important;
    }

    .campo1{
        float            : left;
        width            : 152px;
        text-indent      : 5px;
        background-color : #FFF;
        border-right: 1px solid #d4d4d4;
        white-space:nowrap;
        text-overflow: ellipsis;
        overflow:hidden;
    }

    .campo2{
        float            : left;
        width            : 175px;
        text-indent      : 5px;
        background-color : #FFF;
        border-right: 1px solid #d4d4d4;
        white-space:nowrap;
        text-overflow: ellipsis;
        overflow:hidden;
    }

    .filaBoleta{ background-color:#F3F3F3; }

    .filaBoleta input[type=text]{
        border:0px;
        width: 90%;
        height: 100%;
    }

    .filaBoleta input[type=text]:focus { background: #FFF; }

    #bodyTablaBoletas{
        overflow-x       : hidden;
        overflow-y       : auto;
        width            : 100%;
        height           : calc(100% - 30px);
        background-color : #FFF;
        border-bottom    : 1px solid #d4d4d4;
    }

    #bodyTablaBoletas > div{
        overflow      : hidden;
        height        : 22px;
        border-bottom : 1px solid #d4d4d4;
    }

    #bodyTablaBoletas > div > div { height: 18px; /*background-color : #FFF;*/ padding-top: 4px; }

    .headTablaBoletas{
        overflow      : hidden;
        font-weight   : bold;
        width         : 100%;
        border-bottom : 1px solid #d4d4d4;
        height        : 22px;
    }

    .headTablaBoletas div{
        background-color :#F3F3F3;
        height           : 22px;
        padding-top      : 3;
    }
</style>

    <div id="contenedor_formulario">
        <!-- <div class="loadSaveFormulario" id="loadSaveFormulario_"></div> -->

        <div id="contenedor_tabla_boletas">
            <div class="headTablaBoletas">
                <div class="campo0">&nbsp;</div>
                <div class="campo2">Entidad</div>
                <div class="campo2">Concepto</div>
            </div>
            <div id="bodyTablaBoletas">
                <?php echo $body; ?>
            </div>

        </div>
    </div>

    <script>
        //GUARDAR LAS ENTIDADES DEL EMPLEADO EJ: EPS, ARL, ETC
        function guardarEntidadConcepto(cont) {
            var id_entidad = document.getElementById("id_entidad_"+cont).value;
            var id_concepto = document.getElementById("id_concepto_"+cont).value;

            if (id_entidad==0) { alert("Seleccione la entidad!"); return; }
            if (id_concepto==0) { alert("Seleccione el concepto!"); return; }

            Ext.get("loadFila_"+cont).load({
                url     : "contratos/bd/bd.php",
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : "guardarEntidadConcepto",
                    cont        : cont,
                    id_entidad  : id_entidad,
                    id_concepto : id_concepto,
                    id_empleado : '<?php echo $id_empleado; ?>',
                    id_contrato : '<?php echo $id_contrato; ?>',
                }
            });

        }

        //ELIMINAR LAS ENTIDADES CON SUS CONCEPTOS
        function eliminarEntidadConcepto(cont){
            if (!confirm("Aviso\nRealmente desea eliminar el registro?")) {
                return;
            }

            var id_entidad = document.getElementById("id_entidad_"+cont).value;
            var id_concepto = document.getElementById("id_concepto_"+cont).value;

            Ext.get("loadFila_"+cont).load({
                url     : "contratos/bd/bd.php",
                scripts : true,
                nocache : true,
                params  :
                {
                    opc         : "eliminarEntidadConcepto",
                    cont        : cont,
                    id_entidad  : id_entidad,
                    id_concepto : id_concepto,
                    id_empleado : '<?php echo $id_empleado; ?>',
                    id_contrato : '<?php echo $id_contrato; ?>',
                }
            });
        }

        function ventana_trasladar_concepto(id_concepto,id_entidad,cont) {

            Win_Ventana_trasladar_concepto = new Ext.Window({
                width       : 450,
                height      : 400,
                id          : 'Win_Ventana_trasladar_concepto',
                title       : 'Trasladar',
                modal       : true,
                autoScroll  : false,
                closable    : false,
                autoDestroy : true,
                autoLoad    :
                {
                    url     : 'contratos/trasladar.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        id_empleado : '<?php echo $id_empleado; ?>',
                        id_contrato : '<?php echo $id_contrato ?>',
                        id_concepto : id_concepto,
                        id_entidad  : id_entidad,
                        cont        : cont,
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
                                text        : 'Trasladar',
                                scale       : 'large',
                                iconCls     : 'trasladar',
                                iconAlign   : 'top',
                                hidden      : false,
                                handler     : function(){ BloqBtn(this); generar_traslado() }
                            },
                            {
                                xtype       : 'button',
                                width       : 60,
                                height      : 56,
                                text        : 'Regresar',
                                scale       : 'large',
                                iconCls     : 'regresar',
                                iconAlign   : 'top',
                                hidden      : false,
                                handler     : function(){ BloqBtn(this); Win_Ventana_trasladar_concepto.close(id) }
                            },
                        ]
                    }
                ]
            }).show();
        }

    </script>
