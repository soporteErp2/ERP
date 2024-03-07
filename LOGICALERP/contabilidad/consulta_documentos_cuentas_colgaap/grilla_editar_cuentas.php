<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';

    function filasCuentasUpdateContabilizacion($opcGrilla, $cont, $idCuenta='', $cuenta='', $descripcion='', $debe=0, $haber=0,$id_centro_costos=0 ){
         $style = '';
         if ($idCuenta == '') { $style = 'display:none;'; }

        $return  = '<div class="filaBoleta" id="fila_'.$opcGrilla.'_'.$cont.'">
                        <div class="campo0" style="width:40px !important; overflow:hidden;">
                            <div style="float:left; margin:3px 0 0 2px;">'.$cont.'</div>
                            <div style="float:left; width:18px; overflow:hidden;" id="renderCuenta'.$opcGrilla.'_'.$cont.'"></div>
                        </div>

                        <div class="campo1" style="width:110px;">
                            <input type="text" id="cuenta'.$opcGrilla.'_'.$cont.'" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');" value="'.$cuenta.'" onchange="validarCampoCuenta_'.$opcGrilla.'(this.value,'.$cont.')" />
                        </div>

                        <div onclick="ventanaBuscarCuenta_'.$opcGrilla.'('.$cont.');" title="Buscar Cuenta" class="iconBuscarArticulo">
                            <img src="img/buscar20.png"/>
                        </div>

                        <div class="campo1" style="width:250px;" id="descripcion'.$opcGrilla.'_'.$cont.'" title="'.$descripcion.'">'.$descripcion.'</div>

                        <div class="campo1" style="width:100px;">
                            <input type="text" id="debito'.$opcGrilla.'_'.$cont.'" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');" style="text-align:right;" value="'.$debe.'"/>
                        </div>
                        <div class="campo1" style="width:100px;">
                            <input type="text" id="credito'.$opcGrilla.'_'.$cont.'" onKeyup="validarNumberCuenta'.$opcGrilla.'(event,this,\'double\',\''.$cont.'\');" style="text-align:right;" value="'.$haber.'"/>
                        </div>

                        <div class="campo4" style="width:25px;background-color:#F2F2F2 !important;border-left:0px;'.$style.'" id="divImgEliminar_'.$cont.'" title="Eliminar Cuenta">
                                <img src="img/delete.png" onclick="deleteCuenta_'.$opcGrilla.'('.$cont.')"/>
                        </div>
                        <div style="display:none">
                            <input type="text" id="idCuenta'.$opcGrilla.'_'.$cont.'" value="'.$idCuenta.'" readonly/>
                        </div>

                        <input type="hidden" id="id_centro_costos'.$opcGrilla.'_'.$cont.'" value="'.$id_centro_costos.'" >

                    </div>';

        if($idCuenta > 0){
            $return .=  '<script>
                            arrayContabilidadDb['.$cont.'] = {
                                id_cuenta   : "'.$idCuenta.'",
                                cuenta      : "'.$cuenta.'",
                                descripcion : "'.$descripcion.'",
                                debito      : "'.$debe.'",
                                credito     : "'.$haber.'"
                            };

                            //llamamos la funcion para generar los calculos de la factura
                            //calcTotal'.$opcGrilla.'('.$debe.','.$haber.',"agregar");
                        </script>';
        }

        return $return;
    }
    // echo $tabla_asiento;
    //CONSULTAR TODAS LAS CUENTAS DEL DOCUMENTO
    $sql   = "SELECT
                    id_cuenta,
                    codigo_cuenta,
                    cuenta,
                    debe,
                    haber,
                    id_centro_costos
                FROM  $tabla_asiento
                WHERE activo=1
                    AND id_empresa='$id_empresa'
                    AND id_sucursal='$id_sucursal'
                    AND id_documento='$id_documento'
                    AND tipo_documento='$tipo_documento'
                    ORDER BY codigo_cuenta ASC";
    $query = mysql_query($sql,$link);
    $cont  = 0;
    while ($row = mysql_fetch_array($query)) {
        $bodyArticle .= filasCuentasUpdateContabilizacion($opcGrilla, ++$cont, $row['id_cuenta'], $row['codigo_cuenta'], $row['cuenta'], $row['debe'], $row['haber'],$row['id_centro_costos']);
    }
    $bodyArticle .= filasCuentasUpdateContabilizacion($opcGrilla, ++$cont);
    $bodyArticle .= '<script>contCuentas'.$opcGrilla.' = "'.$cont.'";</script>';
?>
<script>

    var arrayContabilidadDb = [];

    //VARIABLES DE GRILLA
    var debitoAcumulado<?php echo $opcGrilla; ?>  = 0.00
    ,   creditoAcumulado<?php echo $opcGrilla; ?> = 0.00
    ,   total<?php echo $opcGrilla; ?>            = 0.00
    ,   contCuentas<?php echo $opcGrilla; ?>      = 1;

</script>

<div class="contenedorEditarCuentasDocumento" id="contenedorEditarCuentasDocumento">
    <div id="renderUpdateCuenta" style="width: 20px; heigth:22px; overflow:hidden;">&nbsp;</div>
    <div style="width:97%;height:90%;background-color: #CDDBF0;overflow:hidden;margin-left: 10px;">
        <div id="contenedor_formulario_configuracion">
            <div id="contenedor_tabla_configuracion">
                <div class="headTablaBoletas">
                    <div class="campo0" style="width:40px !important;"></div>
                    <div class="campo1" style="width:110px;">Cuenta</div>
                    <div class="campo1" style="width:250px">Descripcion</div>
                    <div class="campo1" style="width:100px;">Debito</div>
                    <div class="campo1" style="width:100px;">Credito</div>
                    <div class="campo4" style="width:25px;border-left:0px;">&nbsp;</div>
                </div>
                <div  id="bodyTablaConfiguracion" style="height: 180px;">
                <?php echo $bodyArticle; ?>
            </div>
            </div>

        </div>
    </div>
</div>

<script>

    function newFilaBody<?php echo $opcGrilla; ?>(cont){
        return  '<div class="campo0" style="width:40px !important; overflow:hidden;">'+
                    '<div style="float:left; margin:3px 0 0 2px;">'+cont+'</div>'+
                    '<div style="float:left; width:18px; overflow:hidden;" id="renderCuenta<?php echo $opcGrilla; ?>_'+cont+'"></div>'+
                '</div>'+
                '<div class="campo1" style="width:110px;">'+
                    '<input type="text" id="cuenta<?php echo $opcGrilla; ?>_'+cont+'" onKeyup="validarNumberCuenta<?php echo $opcGrilla; ?>(event,this,\'double\',\''+cont+'\');" onchange="validarCampoCuenta_<?php echo $opcGrilla; ?>(this.value,'+cont+')" />'+
                '</div>'+
                '<div onclick="ventanaBuscarCuenta_<?php echo $opcGrilla; ?>('+cont+');" title="Buscar Cuenta" class="iconBuscarArticulo">'+
                    '<img src="img/buscar20.png"/>'+
                '</div>'+
                    '<div class="campo1" style="width:250px;" id="descripcion<?php echo $opcGrilla; ?>_'+cont+'"></div>'+
                '<div class="campo1" style="width:100px;">'+
                    '<input type="text" id="debito<?php echo $opcGrilla; ?>_'+cont+'" onKeyup="validarNumberCuenta<?php echo $opcGrilla; ?>(event,this,\'double\',\''+cont+'\');" style="text-align:right; " value="0"/>'+
                '</div>'+
                '<div class="campo1" style="width:100px;">'+
                    '<input type="text" id="credito<?php echo $opcGrilla; ?>_'+cont+'" onKeyup="validarNumberCuenta<?php echo $opcGrilla; ?>(event,this,\'double\',\''+cont+'\');" style="text-align:right; " value="0"/>'+
                '</div>'+
                '<div class="campo4" style="width:25px;background-color:#F2F2F2 !important;display:none;border-left:0px;" title="Eliminar Cuenta" id="divImgEliminar_'+cont+'">'+
                        '<img src="img/delete.png" onclick="deleteCuenta_<?php echo $opcGrilla; ?>('+cont+')"/>'+
                '</div>'+
                '<div style="display:none">'+
                    '<input type="text" id="idCuenta<?php echo $opcGrilla; ?>_'+cont+'" readonly/>'+
                '</div>'+
                '<input type="hidden" id="id_centro_costos<?php echo $opcGrilla; ?>_'+cont+'" value="0" >';
    }

    //================================== VALIDACIO INPUT NUMBER ===================================//
    function validarNumberCuenta<?php echo $opcGrilla; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1]
        ,   idInput     = (input.id).split('_')[0];

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 13){

            if (idInput == 'cuenta<?php echo $opcGrilla; ?>'){ document.getElementById('debito<?php echo $opcGrilla; ?>_'+contIdInput).focus(); }
            else if (idInput == 'debito<?php echo $opcGrilla; ?>'){ document.getElementById('credito<?php echo $opcGrilla; ?>_'+contIdInput).focus(); }

            var id_cuenta   = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value
            ,   cuenta      = document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value
            ,   debito      = document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).value
            ,   credito     = document.getElementById('credito<?php echo $opcGrilla; ?>_'+cont).value;


            if( cont == contCuentas<?php echo $opcGrilla; ?>
                && id_cuenta > 0
                && cuenta > 0
                && (debito > 0 || credito > 0)
                ){

                contCuentas<?php echo $opcGrilla; ?>++;
                var newDiv    = document.createElement('div');
                var divRender = 'bodyDivArticulos<?php echo $opcGrilla; ?>_'+contCuentas<?php echo $opcGrilla; ?>;

                newDiv.innerHTML = newFilaBody<?php echo $opcGrilla; ?>(contCuentas<?php echo $opcGrilla; ?>);
                newDiv.setAttribute('id','fila_<?php echo $opcGrilla; ?>_'+contCuentas<?php echo $opcGrilla; ?>);
                newDiv.setAttribute('class','filaBoleta');
                document.getElementById('bodyTablaConfiguracion').appendChild(newDiv);

                document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+contCuentas<?php echo $opcGrilla; ?>).focus();

                document.getElementById("divImgEliminar_"+cont).style.display="block";
            }
            return true;
        }

        patron = (typeValidate=='double')? /[^\d.]/g : /[^\d]/g;
        if(patron.test(numero)){
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        return true;
    }

    function validarCampoCuenta_<?php echo $opcGrilla; ?>(cuenta,cont){

        if(isNaN(cuenta) || cuenta==''){
            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value    = '';
            document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+cont).value = '';
        }
        else if(cuenta.length<6){

            document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value          = "";
            document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value        = "";
            document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+cont).innerHTML = "";
            document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).focus();
            // return;
        }
        else{
            Ext.get('renderCuenta<?php echo $opcGrilla; ?>_'+cont).load({
                url     : 'consulta_documentos_cuentas_colgaap/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc           : 'consultarCuenta',
                    cont          : cont,
                    cuenta        : cuenta,
                    opcGrilla     : '<?php echo $opcGrilla; ?>',
                    tabla_asiento : '<?php echo $tabla_asiento; ?>',
                }
            });
        }
    }

    function deleteCuenta_<?php echo $opcGrilla ?>(cont){
        // id_departamento  = document.getElementById('id_departamento<?php echo $opcGrilla; ?>_'+cont).value
        id_centro_costos = document.getElementById('id_centro_costos<?php echo $opcGrilla; ?>_'+cont).value;
        if ( id_centro_costos>0) {
            if (confirm('Advertencia\nEsta cuenta tiene centro de costos relacionado Si la elimina se perdera\nRealmente desea eliminar la cuenta?')) {
                document.getElementById("fila_<?php echo $opcGrilla ?>_"+cont).parentNode.removeChild(document.getElementById("fila_<?php echo $opcGrilla ?>_"+cont));
            }
            else{
                return;
            }
        }else{
            document.getElementById("fila_<?php echo $opcGrilla ?>_"+cont).parentNode.removeChild(document.getElementById("fila_<?php echo $opcGrilla ?>_"+cont));
        }

    }

    function save_update_contabilizacion(){
        Ext.getCmp('btnGuardarActualizarCuentas').disable();
        var acumDebito       = 0
        ,   acumCredito      = 0
        ,   diferenciaSaldo  = 0
        ,   contTotalCuentas = 0
        ,   contCuentas      = 0
        ,   jsonCuentas      = {}                   //OBJETO  JAVASCRIPT
        ,   divsCuentas      = document.querySelectorAll(".filaBoleta");

        jsonCuentas['datos'] = {
            id_tercero               : '<?php echo $id_tercero; ?>',
            id_documento             : '<?php echo $id_documento; ?>',
            consecutivo_documento    : '<?php echo $consecutivo_documento; ?>',
            fecha_documento          : '<?php echo $fecha_documento; ?>',
            tipo_documento           : '<?php echo $tipo_documento; ?>',
            tipo_documento_extendido : '<?php echo $tipo_documento_extendido; ?>',
        };

        for(i in divsCuentas){
            if(typeof(divsCuentas[i].id)!='undefined'){
                // console.log(divsCuentas[i].id);
                //VALIDACION CUENTAS PENDIENTES
                contFilaCuenta = (divsCuentas[i].id).split('_')[2];

                var idCuenta         = document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+contFilaCuenta).value
                ,   cuenta           = document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+contFilaCuenta).value
                ,   debe             = document.getElementById('debito<?php echo $opcGrilla; ?>_'+contFilaCuenta).value
                ,   haber            = document.getElementById('credito<?php echo $opcGrilla; ?>_'+contFilaCuenta).value
                ,   descripcion      = document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+contFilaCuenta).value
                ,   id_centro_costos = document.getElementById('id_centro_costos<?php echo $opcGrilla; ?>_'+contFilaCuenta).value;

                if(isNaN(debe)){ debe = 0; }
                if(isNaN(haber)){ haber = 0; }
                if(isNaN(idCuenta) && (debe > 0 ||  haber > 0)){ contCuentas++; }

                //VALIDACION DOBLE PARTIDA
                if(!isNaN(parseFloat(debe))){ acumDebito +=parseFloat(debe); }                  //DEBITO
                if(!isNaN(parseFloat(haber))){ acumCredito +=parseFloat(haber); }               //CREDITO

               if( idCuenta > 0 &&  (debe >= 0 ||  haber >= 0) ){
                     contTotalCuentas++;
                    //ARMA JSON
                    jsonCuentas[contFilaCuenta] = {
                        id_cuenta             : idCuenta,
                        cuenta                : cuenta,
                        descripcion           : descripcion,
                        debito                : debe,
                        credito               : haber,
                        id_centro_costos      : id_centro_costos,
                        id_documento          : '<?php echo $id_documento; ?>',
                        consecutivo_documento : '<?php echo $consecutivo_documento; ?>'
                    }
                }
            }
        }
        // console.log(jsonCuentas);return;
        //ERROR CUENTAS PENDIENTES
        if(contTotalCuentas == 0){ alert("Aviso!\nDebe ingresar una contabilizacion valida!"); Ext.getCmp('btnGuardarActualizarCuentas').enable(); return; }            //TOTAL CUENTAS
        else if(contTotalCuentas == 1){ alert("Aviso!\nLas cuentas no cumplen doble partida."); Ext.getCmp('btnGuardarActualizarCuentas').enable(); return; }           //TOTAL CUENTAS
        else if(contCuentas > 0){ if(!confirm("Aviso!\nHay cuentas pendientes por configurar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ Ext.getCmp('btnGuardarActualizarCuentas').enable(); return; } }   //PENDIENTES

        //ERROR DOBLE PARTIDA
        acumDebito  = (parseFloat(acumDebito).toFixed(2))*1;
        acumCredito = (parseFloat(acumCredito).toFixed(2))*1;

        if(acumDebito > acumCredito){           //ERROR DIFERENCIA DEBITO
            diferenciaSaldo = acumDebito - acumCredito;
            alert("Aviso!\nLas cuentas no cumplen doble partida, hay una diferencia a favor debito de $"+diferenciaSaldo); Ext.getCmp('btnGuardarActualizarCuentas').enable(); return;
        }
        else if(acumDebito < acumCredito){      //ERROR DIFERENCIA CREDITO
            diferenciaSaldo = acumCredito - acumDebito;
            alert("Aviso!\nLas cuentas no cumplen doble partida, hay una diferencia a favor debito de $"+diferenciaSaldo); Ext.getCmp('btnGuardarActualizarCuentas').enable(); return;
        }

        jsonCuentas = JSON.stringify(jsonCuentas);

        Ext.get('renderUpdateCuenta').load({
            url     : 'consulta_documentos_cuentas_colgaap/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc           : 'updateContabilizacionCuentas',
                tabla_asiento : '<?php echo $tabla_asiento ?>',
                jsonCuentas   : jsonCuentas,
            }
        });
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarCuenta_<?php echo $opcGrilla; ?>(cont){
        var myalto  = Ext.getBody().getHeight()
        ,   myancho = Ext.getBody().getWidth();
        var titulo=('<?php echo $tabla_asiento; ?>'=='asientos_colgaap')? 'Colgaap' : 'Niif' ;
        Win_Ventana_buscar_cuenta_nota = new Ext.Window({
            width       : 600,
            height      : 600,
            id          : 'Win_Ventana_buscar_cuenta_nota',
            title       : 'Seleccionar Cuenta '+titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'consulta_documentos_cuentas_colgaap/bd/buscar_cuenta.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opcGrilla     : '<?php echo $opcGrilla; ?>',
                    cargaFuncion  : 'responseVentanaBuscarCuenta_<?php echo $opcGrilla; ?>(id,'+cont+');',
                    tabla_asiento : '<?php echo $tabla_asiento ?>',
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
                    handler     : function(){ Win_Ventana_buscar_cuenta_nota.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarCuenta_<?php echo $opcGrilla; ?>(id,cont){
        var cuenta      = document.getElementById('div_<?php echo $opcGrilla; ?>_cuenta_'+id).innerHTML
        ,   descripcion = document.getElementById('div_<?php echo $opcGrilla; ?>_descripcion_'+id).innerHTML;

        if (cuenta.length < 6) {alert("Error!\nDebe seleccionar una cuenta con minimo 6 digitos"); return;}

        document.getElementById('debito<?php echo $opcGrilla; ?>_'+cont).focus();

        document.getElementById('idCuenta<?php echo $opcGrilla; ?>_'+cont).value    = id;
        document.getElementById('cuenta<?php echo $opcGrilla; ?>_'+cont).value      = cuenta;
        document.getElementById('descripcion<?php echo $opcGrilla; ?>_'+cont).innerHTML = descripcion;

        Win_Ventana_buscar_cuenta_nota.close(id);
    }
</script>