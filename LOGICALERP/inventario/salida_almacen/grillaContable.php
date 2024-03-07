<?php
    include("../../../configuracion/conectar.php");
    include("../../../configuracion/define_variables.php");
    include("../config_var_global.php");
    include("../../funciones_globales/funciones_php/randomico.php");
    include("../../funciones_globales/funciones_javascript/totalesEntradaSalidaAlmacen.php");

    //ARRAY GLOBAL PARA ACTUALIZAR LA CANTIDAD DE ARTICULOS VENDIDOS, EL INDICE VA A SER EL ID DEL ARTICULO Y LA CANTIDAD SU CONTENIDO
    $arrayCantidades;
    
    $empresa         = $_SESSION['EMPRESA'];
    $filtro_sucursal = $_SESSION['SUCURSAL'];
    $bodyArticle     = '';
    $acumScript      = '';
    $estado          = '';
    $fecha           = date('Y-m-d');
    
    ?>
<script>
    var arrayEntradaSaldos = new Array();                  // ARRAY QUE CONTIENE CANTIDADES MAXIMAS CUANDO SE CARGA UNA REMISION 

    var subtotalAcumulado<?php echo $opcGrillaContable; ?>   = 0.00
    ,   descuentoAcumulado<?php echo $opcGrillaContable; ?>  = 0.00
    ,   descuento<?php echo $opcGrillaContable; ?>           = 0.00
    ,   acumuladodescuentoArticulo                           = 0.00
    ,   ivaAcumulado<?php echo $opcGrillaContable; ?>        = 0.00
    ,   total<?php echo $opcGrillaContable; ?>               = 0.00
    ,   contArticulos<?php echo $opcGrillaContable; ?>       = 1
    ,   id_cliente_<?php echo $opcGrillaContable;?>          = 0;
    
    var  timeOutObservacion<?php echo $opcGrillaContable; ?> = ''                       // var time out autoguardado onkeyup campo observaciones    
    ,   codigoCliente<?php echo $opcGrillaContable; ?>       = ''
    ,   nitCliente<?php echo $opcGrillaContable; ?>          = 0   
    ,   nombreCliente<?php echo $opcGrillaContable; ?>       = ''
    ,   nombre_grilla  = 'ventanaBucarArticulo<?php echo $opcGrillaContable; ?>';       //nombre de la grilla cunado se busca un articulo

    var cuentaDebito=0;

    


</script>


<?php
    
  

    // CARGA DOCUMENTOS A UNA FACTURA
    if(isset($idConsecutivoCarga)){

        $campoCantidad = "saldo_cantidad";
        $referencia_consecutivo = "Entrada de Almacen";
        $tablaCarga             = "compras_entradas_almacen";
        $idTablaCargar          = "id_entrada_almacen";
        $tablaCargaInventario   = "compras_entradas_almacen_inventario";
  

        //consultamos los datos de la tabla a cargar y los insertamos en la factura
        $sqlConsulOrden="SELECT nit,id_proveedor,cod_proveedor,proveedor
                        FROM $tablaCarga 
                        WHERE consecutivo='$idConsecutivoCarga' 
                            AND id_sucursal ='$filtro_sucursal' 
                            AND id_bodega   ='$filtro_bodega' 
                            AND id_empresa  ='$empresa'";
        $queryConsulOrden=mysql_query($sqlConsulOrden,$link);

        $nit                        = mysql_result($queryConsulOrden,0,'nit');
        $id_proveedor_carga      = mysql_result($queryConsulOrden,0,'id_proveedor');
        $cod_proveedor_carga     = mysql_result($queryConsulOrden,0,'cod_proveedor');
        $proveedor_carga         = mysql_result($queryConsulOrden,0,'proveedor');
        $documento_vendedor         = $_SESSION['CEDULAFUNCIONARIO'];                                
        $nombre_vendedor            = $_SESSION['NOMBREFUNCIONARIO']; 
        
        $random_factura = responseUnicoRanomico();                                          // CREACION DEL ID UNICO, Y LE INSERTAMOS lOS VALORES QUE SE VAN A CARGAR
        
        //INSERT EN LA TABLA FACTURAS
        $sqlInsertFactura   = "INSERT INTO $tablaPrincipal(id_empresa,random,nit,id_tercero,cod_tercero,tercero,fecha_inicio,id_sucursal,id_bodega,documento_vendedor,nombre_vendedor)
                                VALUES('$empresa','$random_factura','$nit','$id_proveedor_carga','$cod_proveedor_carga','$proveedor_carga','$fecha','$filtro_sucursal','$filtro_bodega','$documento_vendedor','$nombre_vendedor')";
        $queryInsertFactura = mysql_query($sqlInsertFactura,$link);

        if (!$queryInsertFactura) { '<script>alert("Error!\nSe produjo un error y no se cargo la cotizacion a la factura");</script>'; exit; }      //SI NO SE REALIZO EL INSERT

        $sqlSelectIdFactura = "SELECT id FROM $tablaPrincipal WHERE random='$random_factura' LIMIT 0,1";
        $id_salida_almacen   = mysql_result(mysql_query($sqlSelectIdFactura,$link),0,'id');

        //COPIA DE LA TABLA CARGA INVENTARIO A LA TABLA FACTURACION
        $sqlConsultaInventario= "SELECT COI.id,COI.id_inventario,COI.codigo,COI.nombre,COI.$campoCantidad AS cantidad,COI.costo_unitario,
                                        COI.tipo_descuento,COI.descuento, 
                                        COI.valor_impuesto,COI.observaciones, COI.nombre_unidad_medida,COI.cantidad_unidad_medida,
                                        CO.id AS id_documento,CO.consecutivo AS consecutivo_documento 
                                FROM $tablaCargaInventario AS COI 
                                INNER JOIN  $tablaCarga AS CO ON COI.$idTablaCargar=CO.id
                                WHERE CO.consecutivo     ='$idConsecutivoCarga' 
                                    AND COI.activo       = 1 
                                    AND CO.id_sucursal   ='$filtro_sucursal' 
                                    AND CO.id_bodega     ='$filtro_bodega' 
                                    AND CO.id_empresa    ='$empresa'";
        $queryConsultaInventario=mysql_query($sqlConsultaInventario,$link);
        
        //RECORREMOS LOS RESULTADOS Y CREAMOS LA CADENA DEL INSERT
        while ($rowCotizacion=mysql_fetch_array($queryConsultaInventario)) {
            
            $cadenaInsert.="('$id_salida_almacen',
                            '".$rowCotizacion['id_inventario']."',
                            '".$rowCotizacion['cantidad']."',
                            '".$rowCotizacion['costo_unitario']."',
                            '".$rowCotizacion['tipo_descuento']."',
                            '".$rowCotizacion['descuento']."',
                            '".$rowCotizacion['observaciones']."',
                            '".$rowCotizacion['id']."',
                            '".$rowCotizacion['id_documento']."',
                            '".$rowCotizacion['consecutivo_documento']."',
                            '$referencia_consecutivo',
                            '$filtro_sucursal',
                            '$filtro_bodega'),";
        }
        $cadenaInsert=substr($cadenaInsert,0,-1);
        //EJECUTAMOS LA CADENA DEL INSERT
        $sqlInsertArticulos="INSERT INTO $tablaInventario 
                                            ($idTablaPrincipal,
                                            id_inventario,
                                            cantidad,
                                            costo_unitario,
                                            tipo_descuento,
                                            descuento,
                                            observaciones,
                                            id_tabla_inventario_referencia,
                                            id_consecutivo_referencia,
                                            consecutivo_referencia,
                                            nombre_consecutivo_referencia,
                                            id_sucursal,
                                            id_bodega) 
                                VALUES $cadenaInsert";
        $queryInsertArticulos=mysql_query($sqlInsertArticulos,$link);


       //CARGA DATOS EN LA INTERFACE
        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion        = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));           

        
        $acumScript .=  'id_cliente_'.$opcGrillaContable.'                                    = "'.$id_proveedor_carga.'";
                        document.getElementById("codCliente'.$opcGrillaContable.'").value     = "'.$cod_proveedor_carga.'";
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$proveedor_carga.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                        observacion'.$opcGrillaContable.'="'.$observacion_cotizacion.'";

                        codigoCliente'.$opcGrillaContable.' = "'.$cod_proveedor_carga.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$proveedor_carga.'";';

        include("../bd/functions_body_article.php");        
        $bodyArticle = cargaArticulosSave($id_salida_almacen,$observacion,0,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);  
    }

    //============================================ SI NO SE CARGA DOCUMENTO DE REFERENCIA PREVIO ===================================================
    else if(!isset($id_salida_almacen)){

        // CREACION DEL ID UNICO
        $random_factura = responseUnicoRanomico();

        
        $sqlInsert   ="INSERT INTO $tablaPrincipal (id_empresa,random,fecha_inicio,id_sucursal,id_bodega,documento_vendedor,nombre_vendedor)VALUES('$empresa','$random_factura','$fecha','$filtro_sucursal','$filtro_bodega','".$_SESSION['CEDULAFUNCIONARIO']."','".$_SESSION['NOMBREFUNCIONARIO']."')";
        $queryInsert = mysql_query($sqlInsert,$link);
        $sqlSelectId = "SELECT id FROM $tablaPrincipal  WHERE random='$random_factura' LIMIT 0,1";
        $id_salida_almacen  = mysql_result(mysql_query($sqlSelectId,$link),0,'id');
        
    }

    //======================================================= CARGA SI LA SALIDA EXISTE =================================================================
    else{
        
        include("../bd/functions_body_article.php");

        $sql   = "SELECT id_tercero,cod_tercero,nit,tercero,date_format(fecha_inicio,'%Y-%m-%d') AS fecha, observacion, estado,nombre_vendedor,motivo_salida
                FROM $tablaPrincipal  WHERE id='$id_salida_almacen' AND activo = 1";

        $query = mysql_query($sql,$link);

        $nit                      = mysql_result($query,0,'nit');
        $cliente                  = mysql_result($query,0,'tercero');
        $id_cliente               = mysql_result($query,0,'id_tercero');
        $cod_cliente              = mysql_result($query,0,'cod_tercero');
        $fecha                    = mysql_result($query,0,'fecha');
        $estado                   = mysql_result($query,0,'estado');
        $descuento                = mysql_result($query,0,'descuento');
        $nombre_vendedor          = mysql_result($query,0,'nombre_vendedor');
        $motivo_salida            = mysql_result($query,0,'motivo_salida');                                                           

        if ($opcGrillaContable=='FacturaVenta' && $estado=='1') { echo "ESTA FACTURA DE VENTA ESTA CERRADA "; exit; }
     
        $arrayReplaceString = array("\n", "\r","<br>");
        $observacion = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

   
        $acumScript .=  'id_cliente_'.$opcGrillaContable.'                                      = "'.$id_cliente.'";
                        document.getElementById("codCliente'.$opcGrillaContable.'").value     = "'.$cod_cliente.'";                 
                        document.getElementById("nitCliente'.$opcGrillaContable.'").value     = "'.$nit.'";
                        document.getElementById("nombreCliente'.$opcGrillaContable.'").value  = "'.$cliente.'";
                        document.getElementById("fecha'.$opcGrillaContable.'").value          = "'.$fecha.'";
                        document.getElementById("nombreVendedor'.$opcGrillaContable.'").value = "'.$nombre_vendedor.'";
                        document.getElementById("motivoSalida'.$opcGrillaContable.'").value   = "'.$motivo_salida.'";
                        observacion'.$opcGrillaContable.'                                     = "'.$observacion.'";


                        codigoCliente'.$opcGrillaContable.' = "'.$cod_cliente.'";
                        nitCliente'.$opcGrillaContable.'    = "'.$nit.'";
                        nombreCliente'.$opcGrillaContable.' = "'.$cliente.'";';
         
        $bodyArticle = cargaArticulosSave($id_salida_almacen,$observacion,$estado,$opcGrillaContable,$tablaInventario,$idTablaPrincipal,$link);
    }
  
    $habilita = '';
    if($estado=='1'){  $habilita='onclick="javascript: return false;" disabled'; }


    //======================== DOCUMENTOS AGREGADOS A LA PRESENTE SALIDA ======================//
    $acumDocReferencia  = '';
    $margin_left        = '';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM $tablaInventario 
                            WHERE id_consecutivo_referencia>0 AND $idTablaPrincipal='$id_salida_almacen' AND activo=1 AND id_empresa='$empresa'";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){
        $title="Eliminar la orden de compra";
        $acumDocReferencia .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;" id="divDocReferenciaFactura_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                <div class="contenedorInputDocReferenciaFactura">
                                    <input type="text" class="inputDocReferenciaFactura" value=" '.$rowDocReferencia['cod_referencia'].'" readonly/>
                                </div>
                                <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente Entrada" onclick="eliminaDocReferenciaFactura'.$opcGrillaContable.'(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_salida_almacen.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">
                                    <img src="img/delete.png" style="margin: 1px 0 0 1px;"/>
                                </div>
                            </div>';
        $margin_left = 'margin-left:5px';
    }

?>

<div class="contenedorOrdenCompra">

    <!-- Campo Izquierdo -->
    <div class="bodyTop">
        <div class="contInfoFact">
            <div id="terminar<?php echo $opcGrillaContable; ?>"></div>
            <div class="contTopFila">
                <div class="renglonTop">
                    <div class="labelTop">Fecha</div>
                    <div class="campoTop"><input type="text" id="fecha<?php echo $opcGrillaContable; ?>" value="<?php echo $fecha; ?>" readonly></div>
                </div>
                <div class="renglonTop">                    
                    <div class="labelTop">Motivo Salida</div>
                    <div class="campoTop">
                        <select id="motivoSalida<?php echo $opcGrillaContable; ?>" onchange="motivoSalidaAlmacen(this)" >
                            <option value="0" title="">Seleccione...</option>
                            <option title="Salida de mercancia dañada o vencida" value="Bajas de Mercancia">Bajas de Mercancia</option>
                            <option title="Salida de mercancia por obsequio a un cliente" value="Obsequio">Obsequio</option>
                            <option title="Salida de mercancia por" value="Gasto Interno">Gasto Interno</option>
                        </select>    
                    </div>
                </div>
                <div style="float:left;max-width:20px; height:20px;margin-top:17px;overflow:hidden;" id="renderMotivoSalidaAlmacen"></div>
                <div class="renglonTop">
                    <div class="labelTop">Entrada de Almacen</div>
                    <div class="campoTop" style="background-image: url(img/MyGrillaFondo.png);" id="contenedorDocsReferenciaFactura<?php echo $opcGrillaContable; ?>"><?php echo $acumDocReferencia; ?></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Codigo</div>
                    <div class="campoTop"><input type="text" id="codCliente<?php echo $opcGrillaContable; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" ></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Nit</div>
                    <div class="campoTop"><input type="text" id="nitCliente<?php echo $opcGrillaContable; ?>" onKeyup="buscarCliente<?php echo $opcGrillaContable; ?>(event,this);" /></div>
                </div>
                <div class="renglonTop">
                    <div class="labelTop">Tercero</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreCliente<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly/></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaCliente<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Proveedor">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>
                
                <div class="renglonTop">
                    <div class="labelTop">Usuario</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="nombreVendedor<?php echo $opcGrillaContable; ?>" style="width:100%" Readonly value="<?php echo $_SESSION['NOMBREFUNCIONARIO']; ?>"/></div>
                </div>
                <div style="float:left;max-width:20px; height:20px;margin-top:17px;overflow:hidden;" id="renderOpcionEntrada"></div>
                <script>
                    var observacion<?php echo $opcGrillaContable; ?> = '';                 
                    <?php echo $acumScript; ?> 
                </script>
            </div>
        </div>
    </div>

    <div class="bodyArticulos" id="bodyArticulos<?php echo $opcGrillaContable; ?>">
        <div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"><?php echo $bodyArticle; ?></div>
    </div>
</div>

<script>

    //DESHABILITAR EL BOTON DE IMPRIMIR 
    Ext.getCmp("btnExportar<?php echo $opcGrillaContable; ?>").disable();

    //PONER EL FOCO
    document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").focus();   
 
    //==================== FUNCION PARA GUARDAR EL MOTIVO DE LA NOTA Y SELECCIONAR LA CUENTA A MOVER ================//
    function motivoSalidaAlmacen(select){
        //CAPTURAMOS EL VALOR DEL SELECT CON EL MOTIVO DE LA NOTA PARA GUARDARLO EN LA TABLA DE LA SALIDA Y ABRIR
        //LA VENTANA PARA SELECCIONAR LA CUENTA DEL PUC
        motivoSalida=select.value;

        Ext.get('renderMotivoSalidaAlmacen').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarMotivoSalida',
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_salida_almacen; ?>',
                motivoSalida : motivoSalida
            }
        });  

    }

    //==================== VENTANA PARA SELECCIONAR LA CUENTA DEBITO A MOVER =========================//
    function ventanaCuentaPuc(){
        motivoSalida=document.getElementById("motivoSalida<?php echo $opcGrillaContable; ?>").value;
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-120,
            id          : 'Win_Ventana_buscar_cuenta_puc',
            title       : 'Seleccione la cuenta para '+motivoSalida,
            modal       : true,
            autoScroll  : false,
            closable    : true,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'salida_almacen/buscarCuentaPuc.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                          : 'buscar_cotizacionPedido',
                    opcGrillaContable            : '<?php echo $opcGrillaContable; ?>',
                    motivoSalida                 : motivoSalida
                }
            }
        }).show();

    }

    
 
    //==================  GUARDAR LA OBSERVACION DE LA FACTURA ==================================//
    function inputObservacion<?php echo $opcGrillaContable; ?>(event,input){
        tecla  = (input) ? event.keyCode : event.which;
        if(tecla == 13 || tecla == 9){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        }

        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = setTimeout(function(){
            guardarObservacion<?php echo $opcGrillaContable; ?>();
        },1500);

    }

    function guardarObservacion<?php echo $opcGrillaContable; ?>(){
        var observacion = document.getElementById('observacion<?php echo $opcGrillaContable; ?>').value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
        clearTimeout(timeOutObservacion<?php echo $opcGrillaContable; ?>);
        timeOutObservacion<?php echo $opcGrillaContable; ?> = '';

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  : {
                opc            : 'guardarObservacion',
                id             : '<?php echo $id_salida_almacen; ?>',
                tablaPrincipal : '<?php echo $tablaPrincipal; ?>',
                observacion    : observacion
            },
            success :function (result, request){
                        if(result.responseText != 'true'){
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;
                        }else{
                            observacion<?php echo $opcGrillaContable; ?>=observacion;
                        }

                    },
            failure : function(){ alert('Error de conexion con el servidor'); document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value=observacion<?php echo $opcGrillaContable; ?>;}
        });
    }

    //============================= FILTRO TECLA BUSCAR PROVEEDOR ==============================//
    function buscarCliente<?php echo $opcGrillaContable; ?>(event,Input){
        tecla   = (Input) ? event.keyCode : event.which;
        numero  = Input.value;

        if(Input.value != '' && id_cliente_<?php echo $opcGrillaContable;?> == 0 && tecla == 13){ 
            Input.blur();
            ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(Input.value, Input.id);
            return true;
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(Input.value)){ Input.value = (Input.value).replace(/[^0-9]/g,''); }

        else if(id_cliente_<?php echo $opcGrillaContable;?>>0 && contArticulos<?php echo $opcGrillaContable; ?>>1){
            Input.blur();
            if(confirm('Esta seguro de cambiar de proveedor y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){
                ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
            }
            else{
                document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value    = nitCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value    = codigoCliente<?php echo $opcGrillaContable; ?>;
                document.getElementById("nombreCliente<?php echo $opcGrillaContable;?>").value  = nombreCliente<?php echo $opcGrillaContable; ?>;
            }
        }
        else if(id_cliente_<?php echo $opcGrillaContable;?>>0){
            ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input);
        }
        return true;
    }

    function ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(codCliente, inputId){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCliente',
                codCliente        : codCliente,
                inputId           : inputId,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                id                : '<?php echo $id_salida_almacen; ?>'
            }
        });
    }

    function ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="renderFilasArticulo" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor si es una factura
        if ('<?php echo $opcGrillaContable; ?>'=='FacturaVenta') {
            var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
            for(i in checks){ checks[i].checked=false; checks[i].checked=false; }
        }
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'cambiaCliente',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id                : '<?php echo $id_salida_almacen; ?>',
            }
        });
    }

    //============================ VENTANA BUSCAR CLIENTE =======================================//
    function buscarVentanaCliente<?php echo $opcGrillaContable; ?>(){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND tipo_cliente = \"Si\"';

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
            title       : 'Proveedores',
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
                    sql           : sql,
                    cargaFuncion  : 'renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id);',
                    nombre_grilla : 'cliente<?php echo $opcGrillaContable; ?>'
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
                    handler     : function(){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentana<?php echo $opcGrillaContable; ?>(id){

        if(id != id_cliente_<?php echo $opcGrillaContable;?> && contArticulos<?php echo $opcGrillaContable; ?>>1){
            if(!confirm('Esta seguro de cambiar de proveedor y eliminar los articulos relacionados en la <?php echo $opcGrillaContable; ?>')){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }
        }
        else if (id == id_cliente_<?php echo $opcGrillaContable;?>){ Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close(); return; }

        ajaxCambiaCliente<?php echo $opcGrillaContable; ?>(document.getElementById("codCliente<?php echo $opcGrillaContable; ?>"));
        id_cliente_<?php echo $opcGrillaContable;?> = id;
        contArticulos<?php echo $opcGrillaContable; ?>  = 1;

        Win_VentanaCliente_<?php echo $opcGrillaContable; ?>.close();
        ajaxbuscarCliente<?php echo $opcGrillaContable; ?>(id,'idCliente<?php echo $opcGrillaContable; ?>');
    }

    //============================== FILTRO TECLA BUSCAR ARTICULO ==========================================================//
    function buscarArticulo<?php echo $opcGrillaContable; ?>(event,input){
        var contIdInput = (input.id).split('_')[1]
        ,   numero = input.value
        ,   tecla  = (input) ? event.keyCode : event.which;

        if (tecla == 13 && numero>0) {
            input.blur();
            ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(input.value, input.id);
            return true;
        }
        else if(numero == '' || tecla == 18 || tecla == 17 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 16){ return; }

        patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }

        if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
            
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'block';
            document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+contIdInput).style.display = 'inline';
        }
        else if(document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
            document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value         = 0;
            document.getElementById("unidades<?php echo $opcGrillaContable; ?>_"+contIdInput).value           = "";
            document.getElementById("costoArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value      = "";
            document.getElementById("nombreArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value     = "";
            document.getElementById("costoTotalArticulo<?php echo $opcGrillaContable; ?>_"+contIdInput).value = "";
        }
        return true;
    }


    function ajaxBuscarArticulo<?php echo $opcGrillaContable; ?>(valor,input){
        
        var arrayIdInput = input.split('_');
        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+arrayIdInput[1]).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                whereBodega       : 'AND IT.id_sucursal=<?php echo $filtro_sucursal; ?>  AND IT.id_ubicacion=<?php echo $filtro_bodega ?>',
                campo             : arrayIdInput[0],
                valorArticulo     : valor,
                idArticulo        : arrayIdInput[1],
                idCliente         : id_cliente_<?php echo $opcGrillaContable;?>,
                id                : '<?php echo $id_salida_almacen; ?>'
            }
        });
    }

    //======================== FUNCION PARA BUSCAR UNA ENTRADA DE ALMACEN POR SU NUMERO =======================================//
    function buscarCotizacionPedido<?php echo $opcGrillaContable; ?>(event,Input){
        tecla   = (Input) ? event.keyCode : event.which;
        numero  = Input.value;

        if(tecla == 13){
            var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            }
            else if (validacion== 2 || validacion== 0) { ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(Input.value); }
            return;
        }
        patron = /[^\d]/;
        if(patron.test(numero)){ Input.value = numero.replace(patron,''); }
        
        return true;  
    }

    function ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido){
      

        Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>").load({
            url     : "bd/bd.php",
            scripts : true,
            nocache : true,
            params  : 
            {
                opc               : 'cargarDocuementoNewFactura',
                tablaBuscar       : 'compras_entradas_almacen',
                id                : idCotizacionPedido,
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value                      
            }
        });
    }
    //=========================== FUNCION PARA AGREGAR LA COTIZACION-PEDIDO-REMISION ====================================//
    /*********************************************************************************************************************/
    function agregarDocumento<?php echo $opcGrillaContable; ?>(){
        var codDocAgregar = document.getElementById('cotizacionPedido<?php echo $opcGrillaContable; ?>').value;
        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('digite el consecutivo del documento que desea cargar.'); return;}

        Ext.get("renderCargaCotizacionPedido<?php echo $opcGrillaContable; ?>").load({
            url     : "bd/bd.php",
            scripts : true,
            nocache : true,
            params  : 
            {
                opc               : 'agregarDocumento',
                codDocAgregar     : codDocAgregar,
                id_factura        : '<?php echo $id_salida_almacen ?>',
                opcGrillaContable : "<?php echo $opcGrillaContable; ?>",
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value                      
            }
        });
    }

    //======================================= FUNCION CAMBIA PROVEEDOR ==================================================//
    /*********************************************************************************************************************/
    function ajaxCambia<?php echo $opcGrillaContable; ?>(Input){
        // Reset campos Proveedor
        document.getElementById("nombreCliente<?php echo $opcGrillaContable; ?>").value = '';
        document.getElementById("bodyArticulos<?php echo $opcGrillaContable; ?>").innerHTML = '<div class="contTopFila" id="renderizaNewArticulo<?php echo $opcGrillaContable; ?>"></div>';
        if(Input.id != 'codCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("codCliente<?php echo $opcGrillaContable; ?>").value = ''; }
        else if(Input.id != 'nitCliente<?php echo $opcGrillaContable; ?>'){ document.getElementById("nitCliente<?php echo $opcGrillaContable; ?>").value = ''; }

        // Reset Checks Proveedor y se deshabilitan
        var checks = document.getElementById('checksRetenciones<?php echo $opcGrillaContable; ?>').getElementsByTagName('input');
        for(i in checks){ checks[i].checked=false; checks[i].disabled=true; }

        Ext.get("contenedor_facturacion_compras").load({
            url     : "facturacion_compras.php",
            scripts : true,
            nocache : true,
            params  : { filtro_bodega : document.getElementById("filtro_ubicacion_facturacion_compras").value }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(){

        //DATOS DE LA TABLA A CARGAR       
        titulo="Seleccione la Orden de Compra";
        tablaGrilla="compras_entradas_almacen";
        nombreGrillaCotizacionPedido="grillaEntradaAlmacen";
        
         
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>',
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/grillaBuscarCotizacionPedido.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                          : 'buscar_cotizacionPedido',
                    opcGrillaContable            : '<?php echo $opcGrillaContable; ?>',
                    tablaCotizacionPedido        : tablaGrilla,
                    nombreGrillaCotizacionPedido : nombreGrillaCotizacionPedido,
                    filtro_bodega                : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
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
                            handler     : function(){ Win_Ventana_buscar_cotizacionPedido<?php echo $opcGrillaContable; ?>.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

    function cargarDocumento<?php echo $opcGrillaContable; ?>(){
        var idCotizacionPedido = document.getElementById('cotizacionPedido<?php echo $opcGrillaContable; ?>').value;
        if(idCotizacionPedido > 0 ){ ajaxBuscarCotizacionPedido<?php echo $opcGrillaContable; ?>(idCotizacionPedido); }
        else{ return; }
        
    }

    //====================================== VENTANA BUSCAR ARTICULO  =======================================================//
    function ventanaBuscarArticulo<?php echo $opcGrillaContable; ?>(cont){
        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        var sql = 'AND id_sucursal=<?php echo $filtro_sucursal; ?> AND id_ubicacion=<?php echo $filtro_bodega; ?> AND cantidad>0';
        Win_Ventana_buscar_Articulo_factura = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_Articulo_factura',
            title       : 'Seleccionar articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/BusquedaInventariosVentas.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    sql           : sql,
                    nombre_grilla : nombre_grilla,
                    nombreTabla   : 'inventario_totales',
                    cargaFuncion  : 'responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,'+cont+');'
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
                    handler     : function(){ Win_Ventana_buscar_Articulo_factura.close(id) }
                },'-'
            ]
        }).show();
    }

    function responseVentanaBuscarArticulo<?php echo $opcGrillaContable; ?>(id,cont){

        document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus();
        
        
        var costoTotal     = 0
        ,   totalDescuento = 0
        ,   idArticulo     = document.getElementById('ventas_id_item_'+id).innerHTML
        ,   codigo         = document.getElementById('div_'+nombre_grilla+'_codigo_'+id).innerHTML
        ,   costo          = document.getElementById('div_'+nombre_grilla+'_precio_venta_'+id).innerHTML
        ,   unidadMedida   = document.getElementById('unidad_medida_grilla_'+id).innerHTML
        ,   nombreArticulo = document.getElementById('div_'+nombre_grilla+'_nombre_equipo_'+id).innerHTML
        ,   cantidad       = (document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value)*1;
        
         Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  : {
                opc           : 'buscarImpuestoArticulo',
                id            : '<?php echo $id_salida_almacen; ?>',
                id_inventario : idArticulo
            },
            success :function (result, request){
            
                        if(result.responseText >=0){
                            //si ya hay un articulo mostrar la imagen deshacer y actualizar
                            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value > 0){
                                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
                                document.getElementById("divImageSave<?php echo $opcGrillaContable; ?>_"+cont).style.display = 'inline';
                                document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value          = unidadMedida;
                                document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value        = idArticulo;
                                document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value       = codigo;

                                document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = costo;
                                document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = nombreArticulo;
                                document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = result.responseText;
                            
                            }else{
                                document.getElementById('unidades<?php echo $opcGrillaContable; ?>_'+cont).value          = unidadMedida;
                                document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value        = idArticulo;
                                document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).value       = codigo;

                                document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value     = costo;
                                document.getElementById('nombreArticulo<?php echo $opcGrillaContable; ?>_'+cont).value    = nombreArticulo;
                                document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value = result.responseText;
                                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display    = "none";

                            }
                            
                            Win_Ventana_buscar_Articulo_factura.close();
                        }
                        else { 
                             alert(result.responseText+'No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                        }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //============================= FILTRO CAMPO CANTIDAD ARTICULO ==========================================================//
    function cantidadArticulo<?php echo $opcGrillaContable; ?>(cantidad){
    }

    //============================= FILTRO TECLA GUARDAR ARTICULO ==========================================================//
    function guardarAuto<?php echo $opcGrillaContable; ?>(event,input,cont){
        var idInsertArticulo = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value
        ,   tecla = (input) ? event.keyCode : event.which
        ,   value = input.value;

        if(tecla == 13){
            input.blur(); 
            guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont);
        }
        else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }
        else if(idInsertArticulo>0) {
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+cont).style.display    = 'inline';
            document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+cont).style.display = 'inline';
        }

        patron = /[^\d]/g;
        if(patron.test(value)){ input.value = input.value.replace(patron,''); }
        return true;
    }

    function guardarNewArticulo<?php echo $opcGrillaContable; ?>(cont){

        var idInsertArticulo         = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var idInventario<?php echo $opcGrillaContable; ?>      = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo<?php echo $opcGrillaContable; ?>      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo<?php echo $opcGrillaContable; ?>     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;;
        var opc                      = 'guardarArticulo';
        var divRender                = '';
        var accion                   = 'agregar';
        var iva                      = document.getElementById('ivaArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;



        if (idInventario<?php echo $opcGrillaContable; ?> == 0){ alert('El campo articulo es Obligatorio'); setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20); return; }
        else if(cantArticulo<?php echo $opcGrillaContable; ?> < 1 || cantArticulo<?php echo $opcGrillaContable; ?> == ''){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur(); alert('El campo Cantidad es obligatorio'); setTimeout(function(){document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }
        else if(costoArticulo<?php echo $opcGrillaContable; ?> < 1 || costoArticulo<?php echo $opcGrillaContable; ?> == ''){ document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).blur();alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100); return; }

        if (idInventario<?php echo $opcGrillaContable; ?> == 0){
            alert('El campo articulo es Obligatorio');
            setTimeout(function(){ document.getElementById('eanArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },100);
            return;
        }
        else if(cantArticulo<?php echo $opcGrillaContable; ?> < 1 || cantArticulo<?php echo $opcGrillaContable; ?> == ''){
            setTimeout(function(){ document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).focus(); },20);
            setTimeout(function(){ alert('El campo Cantidad es obligatorio'); },80);
            return;
        }


        //VALIDACION SI ES UPDATE O INSERT
        if(idInsertArticulo > 0){
            opc       = 'actualizaArticulo';
            divRender = 'renderArticulo<?php echo $opcGrillaContable; ?>_'+cont;
            accion    = 'actualizar';
        }
        else{
            //VALIDAMOS PARA NO REPETIR FILAS DE LAN GRILLA
            contArticulos<?php echo $opcGrillaContable; ?>++;
            temp=1+cont;
            if (contArticulos<?php echo $opcGrillaContable; ?>>temp) {
                contArticulos<?php echo $opcGrillaContable; ?>--;
                divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
                return;
            }else{                
                divRender = 'bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>;
                var div   = document.createElement('div');
                div.setAttribute('id','bodyDivArticulos<?php echo $opcGrillaContable; ?>_'+contArticulos<?php echo $opcGrillaContable; ?>);
                div.setAttribute('class','bodyDivArticulos<?php echo $opcGrillaContable; ?>');
                document.getElementById('DivArticulos<?php echo $opcGrillaContable; ?>').appendChild(div);
            }

        }

        Ext.get(divRender).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : opc,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                consecutivo       : contArticulos<?php echo $opcGrillaContable; ?>,
                cont              : cont,
                idInsertArticulo  : idInsertArticulo,
                idInventario      : idInventario<?php echo $opcGrillaContable; ?>,
                cantArticulo      : cantArticulo<?php echo $opcGrillaContable; ?>,
                costoArticulo     : costoArticulo<?php echo $opcGrillaContable; ?>,
                iva               : iva,
                id                : '<?php echo $id_salida_almacen; ?>',
            }
        });

        //despues de registrar el primer articulo, habilitamos los botones de nueva factura y terminar factura o si esta o no generada la cotizacion
        
        Ext.getCmp("btnNueva<?php echo $opcGrillaContable; ?>").enable();

        try
          {
            if (document.getElementById('titleDocuemento<?php echo $opcGrillaContable; ?>').innerHTML=='') {Ext.getCmp("btnTerminar<?php echo $opcGrillaContable; ?>").enable();}
            else{Ext.getCmp("btnTerminar<?php echo $opcGrillaContable; ?>").disable();}
          }
        catch(err)
          {
            Ext.getCmp("btnTerminar<?php echo $opcGrillaContable; ?>").enable();
          }
        
        //llamamos la funcion para calcular los totales de la facturan si accion = agregar

        if (accion=="agregar") {
            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,accion,cont);        
        }
    }

    function deleteArticulo<?php echo $opcGrillaContable; ?>(cont){
        //antes de eliminar tomamos las variable para enviarlas a la funcion para recalcular los totales
        var idArticulo<?php echo $opcGrillaContable; ?> = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var cantArticulo<?php echo $opcGrillaContable; ?>      = document.getElementById('cantArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        var costoArticulo<?php echo $opcGrillaContable; ?>     = document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;
        

        if(confirm('Esta Seguro de eliminar este articulo de la factura de compra?')){
            Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'deleteArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    idArticulo        : idArticulo<?php echo $opcGrillaContable; ?>,
                    cont              : cont,
                    id                : '<?php echo $id_salida_almacen; ?>'
                }
            });

            calcTotalDocCompraVenta<?php echo $opcGrillaContable ?>(cantArticulo<?php echo $opcGrillaContable; ?>,costoArticulo<?php echo $opcGrillaContable; ?>,'eliminar',cont);
        }
    }

    //======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA ==========================================//
    function ventanaDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont){
        var id = document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+cont).value;

        Win_Ventana_descripcion_Articulo_factura = new Ext.Window({
            width       : 330,
            height      : 240,
            id          : 'Win_Ventana_descripcion_Articulo_factura',
            title       : 'Observacion articulo ',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'ventanaDescripcionArticulo',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',                   
                    idArticulo        : id,
                    cont              : cont,
                    id                : '<?php echo $id_salida_almacen; ?>'
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    text        : 'Guardar',
                    scale       : 'large',
                    iconCls     : 'guardar',
                    iconAlign   : 'left',
                    handler     : function(){ btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,id); }
                },
                {
                    xtype       : 'button',
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'left',
                    handler     : function(){ Win_Ventana_descripcion_Articulo_factura.close(id) }
                }
            ]
        }).show();
    }

    function btnGuardarDescripcionArticulo<?php echo $opcGrillaContable; ?>(cont,idArticulo){
        var observacion = document.getElementById("observacionArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;
        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacion<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'guardarDescripcionArticulo',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : idArticulo,
                id                : '<?php echo $id_salida_almacen; ?>',
                observacion       : observacion
            }
        });
    }
 

    //===================== CANCELAR LOS CAMBIOS DE UN ARTICULO ===============================================//
    function retrocederArticulo<?php echo $opcGrillaContable; ?>(cont){
        //capturamos el id que esta asignado en la variable oculta
        id_actual=document.getElementById("idInsertArticulo<?php echo $opcGrillaContable; ?>_"+cont).value;

        Ext.get('renderArticulo<?php echo $opcGrillaContable; ?>_'+cont).load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'retrocederArticulo',
                cont              : cont,
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                idArticulo        : id_actual,
                id                : '<?php echo $id_salida_almacen; ?>'
            }
        });
    }

    //===================================== FINALIZAR 'CERRAR' 'GENERAR' ===================================//
    function guardar<?php echo $opcGrillaContable; ?>(){ 
        observacion = document.getElementById("observacion<?php echo $opcGrillaContable; ?>").value;        
        if (cuentaDebito<=0) {
            alert("Debe Seleccionar el motivo de la salida y Seleccionar la cuenta a la que se va a asignar los valores del documento");
            document.getElementById("motivoSalida<?php echo $opcGrillaContable; ?>").focus();
            return;
        }
        var validacion = validarArticulos<?php echo $opcGrillaContable; ?>();
        
        if (validacion==0) { alert("No se puede generar una salida sin articulos relacionados!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return; }
        else if (validacion== 2 || validacion== 0) {  

            idBodega    = document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value;           

            Ext.get('terminar<?php echo $opcGrillaContable; ?>').load({
                url     : 'bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc               : 'terminarGenerar',
                    opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                    id                : '<?php echo $id_salida_almacen; ?>',
                    idBodega          : idBodega,
                    cuentaDebito      : cuentaDebito,
                    observacion       : observacion
                }
            });                     
        }
    }

    //================================================= BUSCAR   ================================================//
    function buscar<?php echo $opcGrillaContable; ?>(){
        var validacion= validarArticulos<?php echo $opcGrillaContable; ?>();
        if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscar<?php echo $opcGrillaContable; ?>(); }
        }
        else if (validacion== 2 || validacion== 0) {ventanaBuscar<?php echo $opcGrillaContable; ?>();}
    }

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
                url     : 'bd/buscarGrillaContable.php',
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
                             xtype      : 'button',
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

    //================================== VALIDACION NUMERICA EN CANTIDAD Y DESCUENTO ===================================//
    function validarNumberArticulo<?php echo $opcGrillaContable; ?>(event,input,typeValidate,cont){
        var contIdInput = (input.id).split('_')[1];    
        var nombreInput = (input.id).split('_')[0];      
        
        for (i=0;i<arrayEntradaSaldos.length;i++){ 
           console.log(arrayEntradaSaldos[i]);
        }

        numero = input.value;
        tecla  = (input) ? event.keyCode : event.which;

        if(tecla == 13){
            if(nombreInput == 'cantArticulo<?php echo $opcGrillaContable; ?>'){ 
                var idArticulo = document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value; 
                if(arrayEntradaSaldos[idArticulo+'_'+cont] < numero){
                    input.blur();
                    alert('la cantidad no puede ser mayor a la ya registrada en la entrada');
                    input.value = arrayEntradaSaldos[idArticulo+'_'+cont];
                    setTimeout(function(){ input.focus(); },100);
                }
                else{ 
                    ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,input.value,'<?php echo $opcGrillaContable; ?>');
                    document.getElementById('costoArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).focus(); 
                }
            }
            return true;
        }

        patron = /[^\d.]/g;
        if(patron.test(numero)){ 
            numero      = numero.replace(patron,'');
            input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
        else{
            document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display    = 'inline';

            if(document.getElementById('idInsertArticulo<?php echo $opcGrillaContable; ?>_'+contIdInput).value > 0){
                document.getElementById('divImageDeshacer<?php echo $opcGrillaContable; ?>_'+contIdInput).style.display = 'inline';
            }
        }
    }

    //=================================== VALIDACION DE LA CANTIDAD DE ARTICULOS EXISTENTES ================================//
    function ajaxVerificaCantidadArticulo<?php echo $opcGrillaContable; ?>(cont,cantidad,opc){
        var id = document.getElementById("idArticulo"+opc+"_"+cont).value;

        Ext.Ajax.request({
            url     : 'bd/bd.php',
            params  : 
            {
                opc             : 'verificaCantidadArticulo',
                id              : id,
                filtro_bodega   : '<?php echo $filtro_bodega; ?>'
            },
            success :function (result, request){  
                        var saldoArticulo = result.responseText;  

                        if((cantidad *1)> (saldoArticulo*1)){  
                            alert("Error!\nLa cantidad ingresada es mayor a la existente\nSolo restan "+saldoArticulo+" unidades");
                            document.getElementById("cantArticulo"+opc+"_"+cont).value='';
                            setTimeout(function(){document.getElementById("cantArticulo"+opc+"_"+cont).focus();},200); 
                        }
                        else if(cantidad < saldoArticulo){ document.getElementById("costoArticulo"+opc+"_"+cont).focus(); } 
                        else{  alert("Error!\nSe produjo un problema con la validacion\nNo se verifico la cantidad del Articulo\nSi el problema persiste comuniquese con el administrador del sistema"); }
                    },
            failure : function(){ alert('Error de conexion con el servidor'); }
        });
    }

    //================================== IMPRIMIR EN PDF ==================================================================//

    function imprimir<?php echo $opcGrillaContable; ?> (){
        window.open("bd/imprimirGrillaContable.php?id=<?php echo $id_salida_almacen; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");                                                                                                                                                          
    }

    //================================== IMPRIMIR EN EXCEL =================================================================//
    function imprimir<?php echo $opcGrillaContable; ?>Excel (){
       window.open("bd/exportarExcelGrillaContable.php?id=<?php echo $id_salida_almacen; ?>&opcGrillaContable=<?php echo $opcGrillaContable; ?>&tablaPrincipal=<?php echo $tablaPrincipal; ?>&idTablaPrincipal=<?php echo $idTablaPrincipal; ?>&tablaInventario=<?php echo $tablaInventario; ?>&tablaRetenciones=<?php echo $tablaRetenciones; ?>");                                                                                                                                                          
    }


    //============================  VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR =======================//
    function validarArticulos<?php echo $opcGrillaContable; ?>(){
        
        var cont = 0
        ,   contTotal = 0
        ,   contArticulo
        ,   nameArticulo
        ,   divsArticulos<?php echo $opcGrillaContable; ?> = document.querySelectorAll(".bodyDivArticulos<?php echo $opcGrillaContable; ?>");
        
        for(i in divsArticulos<?php echo $opcGrillaContable; ?>){

            if(typeof(divsArticulos<?php echo $opcGrillaContable; ?>[i].id)!='undefined'){
                
                contTotal++;

                nameArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[0]
                contArticulo = (divsArticulos<?php echo $opcGrillaContable; ?>[i].id).split('_')[1]


                if(     document.getElementById('idArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).value > 0
                    &&  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/save_true.png'
                    ||  document.getElementById('imgSaveArticulo<?php echo $opcGrillaContable; ?>_'+contArticulo).getAttribute('src') == 'img/reload.png'
                    &&  document.getElementById('divImageSave<?php echo $opcGrillaContable; ?>_'+contArticulo).style.display == 'inline')
                    { cont++; }
            }
            
        }
        // si no se han almacenado articulos retornamos 0
        if(contTotal==1){  return 0; }
        // si hay articulos pendientes por guardar o actualizar retornamos 1
        else if(cont > 0){ return 1; }
        // si toda la validacion esta bien retornamos 2
        else {return 2;}
    }

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
                Ext.get("terminar<?php echo $opcGrillaContable; ?>").load({
                    url  : 'bd/bd.php',
                    scripts : true,
                    nocache : true,
                    params  :
                    {
                        opc               : 'cancelarDocumento',
                        id                : '<?php echo $id_salida_almacen; ?>',
                        opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                        idBodega          : '<?php echo $filtro_bodega; ?>'
                    }
                });
            }; 
        }
    }

    function eliminaDocReferenciaFactura<?php echo $opcGrillaContable; ?>(idDocReferencia,docReferencia,id_factura_venta){
        Ext.get('renderizaNewArticulo<?php echo $opcGrillaContable; ?>').load({
            url     : 'bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminaDocReferencia',
                opcGrillaContable : '<?php echo $opcGrillaContable; ?>',
                id_factura        : '<?php echo $id_salida_almacen; ?>',
                id_doc_referencia : idDocReferencia,
                docReferencia     : docReferencia,
                filtro_bodega     : document.getElementById("filtro_ubicacion_<?php echo $opcGrillaContable; ?>").value
            }
        });
    }

</script>
