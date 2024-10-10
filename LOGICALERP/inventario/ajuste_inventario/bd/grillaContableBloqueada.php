<?php
    include("../../../../configuracion/conectar.php");
    include("../../../../configuracion/define_variables.php");
    include("../config_var_global.php");

    $id_empresa  = $_SESSION['EMPRESA'];
    $id_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle = '';
    $acumScript  = '';
    $estado      = '';
    $fechaActual = date('Y-m-d');
    $divImagen   = '';
    $divAnticipo = '';

    $arrayTypeRetenciones = '';
?>
<script>

    var arrayTypeRetenciones  = new Array();// ARRAY QUE CONTIENE LAS RETENCIONES QUE NO SON DESCONTADAS DEL TOTAL

    //variables para calcular los valores de los costos y totales de la factura
    var subtotalIngreso<?php echo $opcGrillaContable; ?> = 0.00
    ,   subtotalSalida<?php echo $opcGrillaContable; ?>  = 0.00
    ,   total<?php echo $opcGrillaContable; ?>           = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>   = 1
    ,   id_cliente_<?php echo $opcGrillaContable; ?>     = 0;

    var objectRetenciones_<?php echo $opcGrillaContable; ?>=[];

    //Bloqueo todos los botones
    Ext.getCmp("Btn_guardar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_editar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_cancelar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("Btn_restaurar_<?php echo $opcGrillaContable; ?>").disable();
    Ext.getCmp("BtnGroup_Estado1_<?php echo $opcGrillaContable; ?>").show();
    Ext.getCmp("BtnGroup_Guardar_<?php echo $opcGrillaContable; ?>").hide();
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").enable();
    Ext.getCmp("Btn_load_excel_body_nota").disable();

</script>
<?php
    include("functions_body_article.php");

    $user_permiso_editar    = (user_permisos(221,'false') == 'true')? 'Ext.getCmp("Btn_editar_'.$opcGrillaContable.'").enable();' : '';        //editar
    $user_permiso_cancelar  = (user_permisos(222,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_'.$opcGrillaContable.'").enable();' : '';      //calcelar
    $user_permiso_restaurar = (user_permisos(223,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_'.$opcGrillaContable.'").enable();' : '';     //restaurar

    //el campo numero de factura solo esta en la tabla de ventas_facturas, si que verificamos que opcGrillaContable sea ventas facturas para pasar la cadena sql con ese campo, sino se pasa si ese campo
    $sql   = "SELECT
                fecha_documento,
                id_usuario,
                documento_usuario,
                usuario,
                id_tercero,
                cod_tercero,
                nit,
                tercero,
                observacion,
                estado,
                id_centro_costo,
                codigo_centro_costo,
                centro_costo,
                consecutivo,
                consecutivo_remision_venta,
                consecutivo_entrada_almacen,
                ajuste_mensual
            FROM $tablaPrincipal
            WHERE id='$id_documento' AND activo = 1";
    $query=$mysql->query($sql,$mysql->link);

    $consecutivo                 = $mysql->result($query,0,'consecutivo');
    $fecha_documento             = $mysql->result($query,0,'fecha_documento');
    $id_usuario                  = $mysql->result($query,0,'id_usuario');
    $documento_usuario           = $mysql->result($query,0,'documento_usuario');
    $usuario                     = $mysql->result($query,0,'usuario');
    $id_tercero                  = $mysql->result($query,0,'id_tercero');
    $cod_tercero                 = $mysql->result($query,0,'cod_tercero');
    $nit                         = $mysql->result($query,0,'nit');
    $tercero                     = $mysql->result($query,0,'tercero');
    $observacion                 = $mysql->result($query,0,'observacion');
    $estado                      = $mysql->result($query,0,'estado');
    $id_centro_costo             = $mysql->result($query,0,'id_centro_costo');
    $codigo_centro_costo         = $mysql->result($query,0,'codigo_centro_costo');
    $centro_costo                = $mysql->result($query,0,'centro_costo');
    $consecutivo_remision_venta  = $mysql->result($query,0,'consecutivo_remision_venta');
    $consecutivo_entrada_almacen = $mysql->result($query,0,'consecutivo_entrada_almacen');
    $ajuste_mensual              = $mysql->result($query,0,'ajuste_mensual');

    $labelCcos = $codigo_centro_costo.' '.$centro_costo;

    $arrayReplaceString = array("\n", "\r","<br>");
    $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

    $acumScript .=  'document.getElementById("codTercero'.$opcGrillaContable.'").value       = "'.$cod_tercero.'";
                    document.getElementById("nitTercero'.$opcGrillaContable.'").value    = "'.$nit.'";
                    document.getElementById("nombreTercero'.$opcGrillaContable.'").value = "'.$tercero.'";
                    document.getElementById("fecha'.$opcGrillaContable.'").value         = "'.$fecha_documento.'";
                    document.getElementById("remision'.$opcGrillaContable.'").value      = "'.$consecutivo_remision_venta.'";
                    document.getElementById("entrada'.$opcGrillaContable.'").value       = "'.$consecutivo_entrada_almacen.'";
                    document.getElementById("usuario'.$opcGrillaContable.'").value       = "'.$usuario.'";
                    document.getElementById("cCos_'.$opcGrillaContable.'").value         = "'.$labelCcos.'";
                    document.getElementById("AjusteMensualBloqueada").value              = "'.$ajuste_mensual.'";
                    observacion'.$opcGrillaContable.'                                    = "'.$observacion.'";';
                    

    $bodyArticle = cargaArticulosSave($id_documento,$observacion,$estado,$opcGrillaContable,$tablaPrincipal,$tablaInventario,$idTablaPrincipal,$link);

    if ($estado == '1'){ $acumScript .= $user_permiso_editar.$user_permiso_cancelar; }
    else if($estado == '2'){  $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento Cruzado">'; }     //documento cruzado con otro
    else if ($estado == '3') { $acumScript .= $user_permiso_restaurar; }
    else{ $divImagen = '<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Documento de venta Cruzado">'; }

    if ($estado==3) {
        $texto_title="<span style='color:red;text-align: center;font-size: 18px;font-weight: bold;'>Ajuste de Inventario<br>N. ".$consecutivo."</span>" ;
        $acumScript.='document.getElementById("titleDocumento'.$opcGrillaContable.'").innerHTML="'.$texto_title.'";';

    }

?>

<div class="contenedorOrdenCompra" id>

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="render_btns_<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>


                <div class="renglonTop">
                    <div class="labelTop">Codigo Tercero</div>
                    <div class="campoTop"><input type="text" id="codTercero<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitTercero<?php echo $opcGrillaContable; ?>" readonly /></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Tercero                    </div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreTercero<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
                </div>

                <div class="renglonTop" style="width:137px;">
                    <div class="labelTop" style="float:left; width:100%;">Centro de Costo</div>
                    <div class="campoTop"><input type="text" id="cCos_<?php echo $opcGrillaContable; ?>" Readonly/></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                        Remision de Venta
                    </div>
                    <div class="campoTop"><input type="text" id="remision<?php echo $opcGrillaContable; ?>"  readonly></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                        Entrada de Almacen
                    </div>
                    <div class="campoTop"><input type="text" id="entrada<?php echo $opcGrillaContable; ?>" readonly></div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="usuario<?php echo $opcGrillaContable; ?>" readonly ></div>
                </div>

                <div class="renglonTop">
                    <div id="cargaFecha<?php echo $opcGrillaContable; ?>"></div>
                    <div class="labelTop">
                            Ajuste mensual
                    </div>
                    <div class="campoTop"><input type="text" id="AjusteMensualBloqueada" readonly></div>
                </div>

            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    <?php echo $acumScript; ?>

    //======================================== BUSCAR UNA  ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }

    function ventanaBuscar<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>',
            title       : 'Seleccionar ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'ajuste_inventario/bd/buscarGrillaContable.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'buscar_<?php echo $opcGrillaContable; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            height      : 56,
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_buscar_<?php echo $opcGrillaContable; ?>.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }


    //==================================// IMPRIMIR  //==================================//
    //***********************************************************************************//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("ajuste_inventario/bd/imprimirAjusteInventario.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    function imprimirEntradas<?php echo $opcGrillaContable; ?> (){
        window.open("ajuste_inventario/bd/imprimirAjusteInventarioEntradas.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    function imprimirSalidas<?php echo $opcGrillaContable; ?> (){
        window.open("ajuste_inventario/bd/imprimirAjusteInventarioSalidas.php?id=<?php echo $id_documento; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");
    }

    function ventanaConfigurarInforme(){

		Win_Ventana_configurar_separador_ajuste_inventario = new Ext.Window({
		    width       : 500,
		    height      : 250,
		    id          : 'Win_Ventana_configurar_separador_ajuste_inventario',
		    title       : 'Aplicar separador',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../inventario/ajuste_inventario/bd/wizard_ajuste_inventario_xls.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionAjusteInventario',
		        }
		    }
		}).show();
	}

    function imprimirXls (){
        const selectDecimales	  = document.getElementById('separadorDecimalesAI').value,
			  selectMiles 		  = document.getElementById('separadorMilesAI').value
        window.open("ajuste_inventario/bd/imprimirAjusteInventarioXls.php?id=<?php echo $id_documento; ?>&separador_decimales="+selectDecimales+
                                                                        "&separador_miles="+selectMiles);
    }

	function validarSelectAI(separadorId){
		const selectDecimales	  = document.getElementById('separadorDecimalesAI'),
			  selectMiles 		  = document.getElementById('separadorMilesAI')
        
		if(selectDecimales.value === selectMiles.value && separadorId === 'decimales'){
			selectMiles.value = (selectMiles.value === ',')? "." : ",";
		}
		else if(selectDecimales.value === selectMiles.value){
			selectDecimales.value = (selectDecimales.value === ',')? "." : ",";
		}
	}
    // function imprimir<?php echo $opcGrillaContable; ?>Excel (){
    //     window.open("bd/exportar_excel_factura_compra.php?id="+'<?php echo $id_documento; ?>');
    // }
     //============================ CANCELAR UN DOCUMENTO =========================================================================//

    function cancelar<?php echo $opcGrillaContable; ?>(){
        var contArticulos = 0;

        if(!document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>')){ return; }

        arrayIdsArticulos = document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').querySelectorAll('.campoNombreArticulo');

        for(i in arrayIdsArticulos){
            if(arrayIdsArticulos[i].innerHTML != '' ){ contArticulos++; }
        }

        if(contArticulos > 0){
            if(confirm('Esta seguro de Eliminar el presente Documento y su contenido relacionado')){
                cargando_documentos('Cancelando Documento...','');
                Ext.get("render_btns_<?php echo $opcGrillaContable; ?>").load({
                    url  : 'ajuste_inventario/bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_documento; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                        idBodega          : '<?php echo $filtro_bodega; ?>'
                    }
                });
            };
        }
    }

    //=============== FUNCION PARA EDITAR UN DOCUMENTO TERMINADO ==============================================//
    function modificarDocumento<?php echo $opcGrillaContable ?>(){
        var texto = '';
        if ('<?php echo $opcGrillaContable; ?>' == 'RemisionesVenta' || '<?php echo $opcGrillaContable; ?>' == 'FacturaVenta'){ texto = "\nSi lo hace se eliminara el movimiento contable del mismo y\nRegresaran Los articulos al Inventario"; }

        if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?"+texto)) {
            cargando_documentos('Editando Documento...','');
            Ext.get('render_btns_<?php echo $opcGrillaContable; ?>').load({
                url     : 'ajuste_inventario/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'modificarDocumentoGenerado',
                    id                : '<?php echo $id_documento; ?>',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id_bodega         : '<?php echo $filtro_bodega; ?>',
                }
            });
        }
    }

    //=============== FUNCION PARA RESTAURAR UN DOCUMENTO ====================================================//
    function restaurar<?php echo $opcGrillaContable ?>(){
        cargando_documentos('Restaurando Documento...','');
        Ext.get('render_btns_<?php echo $opcGrillaContable ?>').load({
            url     : 'ajuste_inventario/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'restaurarDocumento',
                id                : '<?php echo $id_documento; ?>',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idBodega          : '<?php echo $filtro_bodega; ?>'
            }
        });
    }



</script>
