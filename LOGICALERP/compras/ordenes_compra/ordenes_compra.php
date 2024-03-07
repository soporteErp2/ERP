<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../funciones_globales/funciones_php/randomico.php");

	$id_empresa   = $_SESSION['EMPRESA'];
	$id_sucursal  = $_SESSION['SUCURSAL'];
	$id_usuario   = $_SESSION['IDUSUARIO'];
	$bodyArticle  = '';
	$acumScript   = '';
	$descuento    = 0;
	$fecha_actual = date('Y-m-d');
?>
<script>
	var contArticulosOrdenCompra = 1
	,	codigoProveedor          = 0
	,	nitProveedor             = 0
	,	observacionOrdenCompra   = ''
	,	nombreProveedor          = '';

	arrayIvaOrdenCompra    = [];
	arrayIvaOrdenCompra[0] = { nombre:"", valor:"" };

	//variables para calcular los valores de los costos y totales de la factura
	var subtotalOrdenCompra = 0.00
	,   ivaOrdenCompra      = 0.00
	,   totalOrdenCompra    = 0.00;

	//Bloqueo todos los botones
	Ext.getCmp("Btn_imprimir_orden_compra").disable();
	Ext.getCmp("Btn_guardar_orden_compra").disable();
	Ext.getCmp("Btn_editar_orden_compra").disable();
	Ext.getCmp("Btn_cancelar_orden_compra").disable();
	Ext.getCmp("Btn_restaurar_orden_compra").disable();
	Ext.getCmp("BtnGroup_Estado1_orden_compra").hide();
    Ext.getCmp("BtnGroup_Guardar_orden_compra").show();

</script>
<?php

	$acumScript .= (user_permisos(33,'false') == 'true')? 'Ext.getCmp("Btn_guardar_orden_compra").enable();' : ''; 		//guardar
	$acumScript .= (user_permisos(35,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_orden_compra").enable();' : ''; 	//cancelar

	//===================================== SI NO EXISTE ORDEN DE COMPRA CREACION DEL ID UNICO =========================================//
	if(!isset($id_orden_compra)){
		$random_orden_compra = responseUnicoRanomico();

		$sqlInsertOrden   = "INSERT INTO compras_ordenes(random,fecha_inicio,fecha_vencimiento,id_empresa,id_sucursal,id_bodega,id_usuario)
								VALUES('$random_orden_compra','$fecha_actual','$fecha_actual','$id_empresa','$id_sucursal','$filtro_bodega','$id_usuario')";
		$queryInsertOrden = mysql_query($sqlInsertOrden,$link);

		$sqlSelectIdOrden = "SELECT id FROM compras_ordenes WHERE random='$random_orden_compra' LIMIT 0,1";
		$id_orden_compra  = mysql_result(mysql_query($sqlSelectIdOrden,$link),0,'id');
		$descuento        = 0;

		$acumScript .= 	'new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 135,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fechaOrdenCompra",
    					    editable   : false,
    					    value      : "'.$fecha_actual.'",
    					    listeners  : { select: function(combo, value) { guardaFechaOrdenCompra(this);  } }
    					});

    					new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 135,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fechaVencimientoOrdenCompra",
    					    editable   : false,
    					    value      : "'.$fecha_actual.'",
    					    listeners  : { select: function(combo, value) { guardaFechaOrdenCompra(this);  } }
    					});

						id_proveedor_orden_compra = 0;
						document.getElementById("codProveedor").focus();
						document.getElementById("titleDocuementoOrdenCompra").innerHTML="";';
	}
	//================================================= SI EXISTE ORDEN DE COMPRA ======================================================//
	else{
		include("bd/functions_body_article.php");

		$sql   = "SELECT
                        id_proveedor,
                        nit,
                        cod_proveedor,
                        proveedor,
                        estado,
                        observacion,
                        date_format(fecha_inicio,'%Y-%m-%d') AS fecha_inicio,
                        date_format(fecha_vencimiento,'%Y-%m-%d') AS fecha_vencimiento,
                        area_solicitante
					FROM compras_ordenes WHERE id='$id_orden_compra' AND activo = 1 LIMIT 0,1";
		$query = mysql_query($sql,$link);

        $nit               = mysql_result($query,0,'nit');
        $proveedor         = mysql_result($query,0,'proveedor');
        $id_proveedor      = mysql_result($query,0,'id_proveedor');
        $cod_proveedor     = mysql_result($query,0,'cod_proveedor');
        $estadoOrdenCompra = mysql_result($query,0,'estado');
        $fecha_inicio      = mysql_result($query,0,'fecha_inicio');
        $fecha_vencimiento = mysql_result($query,0,'fecha_vencimiento');
        $area_solicitante  = mysql_result($query,0,'area_solicitante');

		$arrayReplaceString     = array("\n", "\r","<br>");
		$observacionOrdenCompra = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

		$acumScript .= 	'new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 135,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fechaOrdenCompra",
    					    editable   : false,
    					    value      : "'.$fecha_inicio.'",
    					    listeners  : { select: function(combo, value) { guardaFechaOrdenCompra(this);  } }
    					});

    					new Ext.form.DateField({
    					    format     : "Y-m-d",
    					    width      : 135,
    					    allowBlank : false,
    					    showToday  : false,
    					    applyTo    : "fechaVencimientoOrdenCompra",
    					    editable   : false,
    					    value      : "'.$fecha_vencimiento.'",
    					    listeners  : { select: function(combo, value) { guardaFechaOrdenCompra(this);  } }
    					});

                        document.getElementById("nitProveedor").value                         = "'.$nit.'";
                        document.getElementById("codProveedor").value                         = "'.$cod_proveedor.'";
                        document.getElementById("nombreProveedor").value                      = "'.$proveedor.'";
                        document.getElementById("areaSolcitante'.$opcGrillaContable.'").value = "'.$area_solicitante.'";

						id_proveedor_orden_compra = "'.$id_proveedor.'";
						observacionOrdenCompra    =" '.$observacionOrdenCompra.'";
						codigoProveedor           = "'.$cod_proveedor.'";
						nitProveedor              = "'.$nit.'";
						nombreProveedor           = "'.$proveedor.'";';

	    if($estadoOrdenCompra == 2){ echo '<div style="font-size:18px;">NO SE PERMITE LA CARGA DE UNA ORDEN DE COMPRA CERRADA EN ESTA PAGINA!</div>'; exit; }
	    else if($estadoOrdenCompra == 3){ echo '<div style="font-size:18px;">NO SE PERMITE LA CARGA DE UNA ORDEN DE COMPRA CANCELADA EN ESTA PAGINA!</div>'; exit; }
	    $bodyArticle = cargaArticulosOrdenCompraSave($id_orden_compra,$observacionOrdenCompra,$estadoOrdenCompra,$link);
	}

	//======================== DOCUMENTOS AGREGADOS AL PRESENTE OCUMENTO ======================//
    $acumDocReferencia  = '';
    $margin_left        = 'margin-left:5px';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM compras_ordenes_inventario
                            WHERE id_consecutivo_referencia>0 AND id_orden_compra='$id_orden_compra' AND activo=1
                            ORDER BY id ASC";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

        if($rowDocReferencia['doc_referencia'] == 'RQ'){ $title = 'Eliminar los Articulos de la Requisicion'; }
        else if($rowDocReferencia['doc_referencia'] == 'O'){ $title = 'Eliminar los Articulos de la Orden de Compra'; }

        $typeDocCruce   = $rowDocReferencia['doc_referencia'];
        $numeroDocCruce = $rowDocReferencia['consecutivo_referencia'];

        $acumDocReferencia .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferenciaOrdenCompra_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                    <div class="contenedorInputDocReferenciaFactura">
                                        <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" style="border-bottom: 1px solid #d4d4d4;" readonly/>
                                    </div>
                                    <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente orden de compra" onclick="eliminaDocReferenciaOrdenCompra(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_documento.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;">
                                        <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btnOrdenCompra_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                                        </div>
                                    </div>
                              </div>';
    }

    // FORMAS DE PAGO
    $formasPago      = '';
    $idFormaPago     = '';
    $arrayFormasPago = 'var idFechaSavePagoFactura = "";
                        var arrayFormaPagoFacutraCompra = new Array();';

    $sqlFormasPago   = "SELECT id,nombre,plazo FROM configuracion_formas_pago WHERE activo=1 AND id_empresa='$id_empresa'";
    $queryFormasPago = mysql_query($sqlFormasPago,$link);

    while ($rowFormasPago=mysql_fetch_array($queryFormasPago)) {
        if ($idFormaPago=='') {
            $idFormaPago     = $rowFormasPago['id'];
            $diasFormaPago   = $rowFormasPago['plazo'];
            $nombreFormaPago = $rowFormasPago['nombre'];
            $arrayFormasPago .= 'idFechaSavePagoFactura = "'.$idFormaPago.'";';
        }
        $formasPago      .= '<option value="'.$rowFormasPago['id'].'" >'.$rowFormasPago['nombre'].'</option>';
        $arrayFormasPago .= 'arrayFormaPagoFacutraCompra['.$rowFormasPago['id'].'] = "'.$rowFormasPago['plazo'].'";';
    }


?>
<div class="contenedorOrdenCompra">
	<!-- Campo Izquierdo -->
	<div class="bodyTop">
		<div class="contInfoFact">
			<div id="render_btn_guardar"></div>
			<div class="contTopFila">
				<div class="renglonTop">
                    <div class="labelTop">Fecha de inicio</div>
                    <div class="campoTop"><input type="text" id="fechaOrdenCompra"></div>
                </div>
                <div class="renglonTop" >
                    <div class="labelTop">Fecha de Vencimiento</div>
                    <div class="campoTop"><input type="text" id="fechaVencimientoOrdenCompra"></div>
                </div>

                <div class="renglonTop" style="width:137px; ">
                    <div class="labelTop" style="float:left; width:100%;">Forma de pago</div>
                    <div id="renderSelectFormaPago" style="float:left; margin-left:-20px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="overflow:hidden;">
                        <select id="selectFormaPagoOrdenCompra" onChange="UpdateFormaPagoOrdenCompra(this.value)" style="float:left;"/>
                            <option>Seleccione...</option>
                            <?php echo $formasPago; ?>
                        </select>
                    </div>
                </div>

                <div class="renglonTop">
                    <div class="labelTop">Docs. Cruce</div>
                    <div class="campoTop" style="height:auto;"  id="contenedorDocsReferenciaOrdenCompra"><?php echo $acumDocReferencia; ?></div>
                </div>
				<div class="renglonTop">
					<div class="labelTop">Codigo Proveedor</div>
					<div class="campoTop"><input type="text" id="codProveedor" onKeyup="buscarProveedorOrdenCompra(event,this);"/></div>
				</div>
				<div class="renglonTop">
					<div class="labelTop">Nit</div>
					<div class="campoTop"><input type="text" id="nitProveedor" onKeyup="buscarProveedorOrdenCompra(event,this);"/></div>
				</div>
				<div class="renglonTop">
                    <div id="renderProveedorOC" style="float:left; /*margin-left:-20px;*/ width:20px; height:19px; overflow:hidden;"></div>
					<div class="labelTop">Proveedor</div>
					<div class="campoTop" style="width:277px"><input type="text" id="nombreProveedor" Readonly/></div>
					<div class="iconBuscarProveedor" onclick="buscarVentanaProveedorOrdenCompra()" title="Buscar proveedor">
						<img src="img/buscar20.png"/>
					</div>
				</div>

<?php
// ---------------------------------------------COMBO DE TIPO DE ORDEN DE COMPRA-------------------------------------------

	$orden_tipo      = '<option value="0" >Seleccione...</option>';
	$sqlOrden_tipo   = "SELECT id,nombre FROM compras_ordenes_tipos	WHERE activo =1 AND id_empresa='$id_empresa'";
	$queryOrden_tipo = mysql_query($sqlOrden_tipo,$link);

	$sql_id          = "SELECT id_tipo FROM compras_ordenes WHERE id = $id_orden_compra";
	$id_tipo         = mysql_result(mysql_query($sql_id,$link),0,'id_tipo');


    while ($rowOrden_tipo=mysql_fetch_array($queryOrden_tipo)) {
        if ($idOrden_tipo == ''){
            $idOrden_tipo = $rowOrden_tipo['id'];
            //$cuentaPagoNiif     = $rowCuentasPago['cuenta_niif'];
            $nombreOrdenTipo = $rowOrden_tipo['nombre'];
        }
		$selected   = ($rowOrden_tipo['id'] == $id_tipo)? 'selected': '';
		$orden_tipo .= '<option value="'.$rowOrden_tipo['id'].'"'.$selected.'>'.$rowOrden_tipo['nombre'].'</option>';
    }

    if ($orden_tipo=='') {
        echo'<script>
                alert("Error!\nNo hay ningun tipo de orden configurado\nDirijase al panel de control->Tipos ordenes de compra\nCree uno y vuelva a intentarlo");
                //Ext.getCmp("Btn_cancelar_FacturaCompra").enable();
            </script>';
        exit;
    }
//--------------------------------------------------------------------------------------------------------------------------
?>
               	 <div class="renglonTop">
					<div class="labelTop">Tipo</div>
					<div id="renderSelectTipoOrden" style="float:left; margin-top:-20px; width:20px; height:19px; overflow:hidden;"></div>
					<div class="campoTop">
						<select id="selectTipoOrdenCompra" onChange="UpdateOrdenCompra(this.value)" style="float:left;"/>
                            <?php echo $orden_tipo; ?>
                        </select>
					</div>
				</div>
                <!-- SELECCION DE AREA O DEPARTAMENTO -->
                <div class="renglonTop">
                    <div class="labelTop">Area o Departamento Solicitante</div>
                    <div id="loadAreaSolicitante" style="float:right; margin-left:-25px; margin-top: -18px; width:20px; height:19px; overflow:hidden;"></div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="areaSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly></div>
                    <div class="iconBuscarProveedor" onclick="buscarVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>()" id="imgBuscarProveedor" title="Buscar Solicitante">
                       <img src="img/buscar20.png"/>
                    </div>
                </div>

			</div>
		</div>
	</div>

	<!-- RENDERIZA LA GRILLA -->
	<div class="bodyArticulos" id="bodyArticulos">
		<div class="renderFilasArticulo" id="renderizaNewArticuloOrdenCompra"><?php echo $bodyArticle; ?></div>
	</div>
</div>

<script>

    //================================== UPDATE FORMAS DE PAGO ====================================//
    function UpdateFormaPagoOrdenCompra(idFormaPago){
        Ext.get('renderSelectFormaPago').load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc         : 'UpdateFormaPago',
                idFormaPago : idFormaPago,
                id          : '<?php echo $id_orden_compra; ?>'
            }
        });
    }

	<?php echo $acumScript; ?>
	var timeOutObservacionOrdenCompra = ''; 	// var time out autoguardado onkeydows campo observaciones

	var observacionOrdenCompra = '';

	//========================// GUARDAR LAS FECHAS DE LA ORDEN //========================//
	function guardaFechaOrdenCompra(inputDate){
        var idInputDate  = inputDate.getEl().id
        ,   valInputDate = inputDate.value;

        Ext.Ajax.request({
            url     : 'ordenes_compra/bd/bd.php',
            params  :
            {
				opc           : 'guardarFechaOrden',
				idInputDate   : idInputDate,
				valInputDate  : valInputDate,
				idOrdenCompra : '<?php echo $id_orden_compra; ?>'
            },
            success :function (result, request){
                        if(result.responseText == 'true'){
                            if(idInputDate=='fechaOrdenCompra'){ fecha_inicio=valInputDate; }
                            else if(idInputDate=='fechaVencimientoOrdenCompra'){ fecha_final=valInputDate; }
                        }
                        else{
                            if(idInputDate=='fechaOrdenCompra'){ document.getElementById(idInputDate).value= valInputDate; }
                            else if(idInputDate=='fechaVencimientoOrdenCompra'){ document.getElementById(idInputDate).value= valInputDate; }
                            alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
                            // console.log(result.responseText);
                        }
                    },
            failure : function(){
                        if(idInputDate=='fechaOrdenCompra'){ document.getElementById(idInputDate).value= valInputDate; }
                        else if(idInputDate=='fechaVencimientoOrdenCompra'){ document.getElementById(idInputDate).value= valInputDate; }
                        alert('Error de conexion con el servidor');
                    }
        });
    }

    //======================== FUNCION PARA BUSCAR LA COTIZACION-PEDIDO POR SU NUMERO =======================================//
    function buscarDocumentoCruceOrdenCompra(event,Input){
        var tecla   = (Input) ? event.keyCode : event.which
        ,   numero  = Input.value;

        if(tecla == 13 || tecla == 9){
            var validacion= validarArticulosOrdenCompra();
            if (validacion==1) {
                if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ajaxBuscarDocumentoCruceOrdenCompra(Input.value); }
            }
            else if (validacion== 2 || validacion== 0) { ajaxBuscarDocumentoCruceOrdenCompra(Input.value); }
            return;
        }

        setTimeout(function(){ Input.value = (Input.value).replace(/[^0-9]/g,''); },10);
    }

    function ajaxBuscarDocumentoCruceOrdenCompra(idCotizacionPedido){

        agregarDocumentoOrdenCompra(idCotizacionPedido);
        return;
        //ESTE CODIGO NO LO EJECUTARA

        if (document.getElementById("imgCargarDesdeOrdenCompra").getAttribute("src")=="img/cotizacion.png") {  //carga remision
            tablaBuscar = "ventas_cotizaciones";
            opcCargar   = "cotizacionRemision";
        }
        else{                                  //cargar Remision desde un pedido
            tablaBuscar = "ventas_pedidos";
            opcCargar   = "pedidoRemision";
        }

        Ext.get('renderCargaCotizacionPedidoOrdenCompra').load({
            url     : "ordenes_compra/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'buscarCotizacionPedido',
                opcCargar         : opcCargar,
                tablaBuscar       : tablaBuscar,
                id                : idCotizacionPedido,
                opcGrillaContable : "OrdenCompra",
                filtro_bodega     : document.getElementById("filtro_ubicacion_ordenes_compra").value
            }
        });
    }

    //========================= FUNCION PARA AGREGAR UN DOCUMENTO ===================================================//
    function agregarDocumentoOrdenCompra(codigo){

        if (codigo!='') { var codDocAgregar=codigo; }
        else{ var codDocAgregar = document.getElementById('cotizacionPedidoOrdenCompra').value; }

        if(isNaN(codDocAgregar) || codDocAgregar==0){ alert('digite el consecutivo del documento que desea cargar.'); return;}
        if (document.getElementById("imgCargarDesdeOrdenCompra").getAttribute("src")=="img/cotizacion.png") { typeDoc = "requisicion"; }
        else if (document.getElementById("imgCargarDesdeOrdenCompra").getAttribute("src")=="img/pedido.png"){ typeDoc = "orden_compra"; }

        Ext.get("renderCargaCotizacionPedidoOrdenCompra").load({
            url     : "ordenes_compra/bd/bd.php",
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'agregarDocumento',
                typeDoc           : typeDoc,
                codDocAgregar     : codDocAgregar,
                id_factura        : '<?php echo $id_orden_compra; ?>',
                opcGrillaContable : "OrdenCompra",
                filtro_bodega     : document.getElementById("filtro_ubicacion_ordenes_compra").value
            }
        });
    }

    function eliminaDocReferenciaOrdenCompra(idDocReferencia,docReferencia,id_factura_venta){
        Ext.get('renderizaNewArticuloOrdenCompra').load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc               : 'eliminaDocReferencia',
                opcGrillaContable : 'OrdenCompra',
                id_factura        : '<?php echo $id_orden_compra; ?>',
                id_doc_referencia : idDocReferencia,
                docReferencia     : docReferencia,
                filtro_bodega     : document.getElementById("filtro_ubicacion_ordenes_compra").value
            }
        });
    }

	/*================================== BUSCAR PROVEEDOR =====================================*/
	function buscarProveedorOrdenCompra(event,Input){
		var tecla   = Input ? event.keyCode : event.which
        ,   inputId = Input.id
        ,   numero  = Input.value;

        if(inputId == "nitProveedor" && numero==nitProveedor){ return true;}
        else if(inputId == "codProveedor" && numero==codigoProveedor){ return true;}
        else if(id_proveedor_orden_compra == 0 && Input.value != '' && tecla == 13){
        	Input.blur();
            ajaxBuscarProveedorOrdenCompra(Input.value, Input.id);
			return true;
		}
		else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

		patron = /[^\d]/;
		if(patron.test(numero)){ Input.value = numero.replace(patron,''); }
        else if(id_proveedor_orden_compra>0 && contArticulosOrdenCompra>1 && tecla==13){
			ajaxBuscarProveedorOrdenCompra(Input.value, Input.id);

		}
		return true;
	}

	function ajaxBuscarProveedorOrdenCompra(codProveedor, inputId){
        var urlRender = (id_proveedor_orden_compra>0 && contArticulosOrdenCompra>1)? 'renderProveedorOC' : 'renderizaNewArticuloOrdenCompra'
        ,   evt       = (id_proveedor_orden_compra>0 && contArticulosOrdenCompra>1)? 'update' : 'insert';


		Ext.get(urlRender).load({
			url		: 'ordenes_compra/bd/bd.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
                opc           : 'buscarProveedor',
                codProveedor  : codProveedor,
                inputId       : inputId,
                idOrdenCompra : '<?php echo $id_orden_compra; ?>',
                evt           : evt
			}
		});
	}

	function buscarVentanaProveedorOrdenCompra(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

		var sql = 'AND tipo_proveedor = \"Si\"';
		Win_VentanaProveedor_orden_compra = new Ext.Window({
			width       : myancho-100,
            height      : myalto-50,
			id			: 'Win_VentanaProveedor_orden_compra',
			title		: 'Proveedores',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		: '../funciones_globales/grillas/BusquedaTerceros.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					sql           : sql,
					cargaFuncion  : 'renderizaResultadoVentanaProveedor(id);',
					nombre_grilla : 'proveedorOrdenCompra',
					quitarWidth   : 150,
					quitarHeight  : 170
				}
			},
			tbar		:
			[
				{
					xtype		: 'button',
					width		: 60,
					height		: 56,
					text		: 'Regresar',
					scale		: 'large',
					iconCls		: 'regresar',
					iconAlign	: 'top',
					handler 	: function(){ Win_VentanaProveedor_orden_compra.close(id) }
				},'-'
			]
		}).show();
	}

	function renderizaResultadoVentanaProveedor(id){
		// var eliminaArticulos = "false";

		// if(id != id_proveedor_orden_compra && contArticulosOrdenCompra>1){
			// if(!confirm('Esta seguro de cambiar de proveedor y eliminar los articulos relacionados en la presente orden de compra')){ return; }
			// else{ eliminaArticulos = "true"; }
		// }
		// else if (id == id_proveedor_orden_compra){ Win_VentanaProveedor_orden_compra.close(); return; }

		// id_proveedor_orden_compra = id;
		// contArticulosOrdenCompra  = 1;

		Win_VentanaProveedor_orden_compra.close();
		ajaxBuscarProveedorOrdenCompra(id,'idProveedor');
	}

    //================================ ARTICULOS ORDEN COMPRA ===================================//
	function guardarAutoOrdenCompra(event,input,cont){
		var tecla = input? event.keyCode : event.which
		,	value = input.value;

		if(tecla == 13){
			input.blur();
			guardarNewArticuloOrdenCompra(cont);
			return true;
		}
		else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

        patron = /[^\d.]/g;
        if(patron.test(value)){
			value      = value.replace(patron,'');
			input.value = value;
        }
        else if(isNaN(value)){ input.value = value.substring(0, value.length-1); }
	    else{
			document.getElementById('divGuardarNewArticuloOrdenCompra_'+cont).style.display    = 'block';
			if(document.getElementById('idInsertArticulo_'+cont).value > 0){
				document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+cont).style.display = 'block';
			}
		}

        return true;
	}

	function guardarNewArticuloOrdenCompra(cont){
		var imgGuardar        = document.getElementById('divGuardarNewArticuloOrdenCompra_'+cont).style.display;
		var imgActualizar     = document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+cont).style.display;

		var idInsertArticulo  = document.getElementById('idInsertArticulo_'+cont).value;
		var idInventario      = document.getElementById('idArticulo_'+cont).value;
		var cantArticulo      = document.getElementById('cantArticulo_'+cont).value;
		var descuentoArticulo = document.getElementById('descuentoArticulo_'+cont).value;
		var costoArticulo     = document.getElementById('costoArticulo_'+cont).value;
		var opc               = 'guardarArticuloOrdenCompra';
		var divRender         = '';
		var iva               = document.getElementById('ivaArticuloOrdenCompra_'+cont).value;
		var accion            = 'agregar';
		var tipoDescuento     = ((document.getElementById('imgDescuentoArticuloOrdenCompra_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

		if(imgGuardar == 'none' && imgActualizar =='none'){ return; }
		if (imgGuardar == 'block' || imgGuardar == 'inline' || imgGuardar == '' ) {document.getElementById('divGuardarNewArticuloOrdenCompra_'+cont).style.display='none';}
		if(isNaN(descuentoArticulo)){
			setTimeout(function(){ document.getElementById('descuentoArticulo_'+cont).focus(); },20);
			setTimeout(function(){ alert('El campo descuento debe ser numerico'); },80);
			return;
		}
		else if (idInventario == 0){
			alert('El campo articulo es Obligatorio');
			setTimeout(function(){ document.getElementById('eanArticulo_'+cont).focus(); },100);
			return;
		}
		else if(cantArticulo <= 0 || cantArticulo == ''){
			setTimeout(function(){ document.getElementById('cantArticulo_'+cont).focus(); },20);
			setTimeout(function(){ alert('El campo Cantidad es obligatorio'); },80);
			return;
		}
        else if(costoArticulo == 0 || isNaN(costoArticulo)){ alert('El campo costo es obligatorio'); setTimeout(function(){document.getElementById('costoArticulo_'+cont).focus(); },20); return; }


		//VALIDACION SI ES UPDATE O INSERT
		if(idInsertArticulo > 0){
			opc       = 'updateArticuloOrdenCompra';
			divRender = 'renderArticuloOrdenCompra_'+cont;
			accion 	  = 'actualizar';
		}
		else{
			contArticulosOrdenCompra++;
			divRender = 'bodyDivArticulos_'+contArticulosOrdenCompra;
			var div   = document.createElement('div');
			div.setAttribute('id','bodyDivArticulos_'+contArticulosOrdenCompra);
			div.setAttribute('class','bodyDivArticulos');
			document.getElementById('DivArticulos').appendChild(div);
		}

		Ext.get(divRender).load({
			url		: 'ordenes_compra/bd/bd.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc               : opc,
				cont              : cont,
				consecutivo       : contArticulosOrdenCompra,
				idInsertArticulo  : idInsertArticulo,
				idInventario      : idInventario,
				cantArticulo      : cantArticulo,
				tipoDescuento     : tipoDescuento,
				descuentoArticulo : descuentoArticulo,
				costoArticulo     : costoArticulo,
				iva               : iva,
				idOrdenCompra     : '<?php echo $id_orden_compra; ?>'
			}
		});
		calcularValoresOrdenCompra(cantArticulo,descuentoArticulo,costoArticulo,accion,tipoDescuento,iva,cont);		// FUNCIONA CALC TOTAL FACTURA
	}

	//===================== BUSCAR ARTICULO ======================//
	function buscarArticuloOrdenCompra(event,input){
		var contIdInput = (input.id).split('_')[1]
		,	numero = input.value
		,	tecla  = (input) ? event.keyCode : event.which;

		if (tecla == 13 && input.value > 0) {
			input.blur();
	    	ajaxBuscarArticuloOrdenCompra(input.value, input.id);
	    	return true;
	    }
	    else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

	    patron = /[^\d]/;
        if(patron.test(numero)){ input.value = numero.replace(patron,''); }
		if(document.getElementById('idInsertArticulo_'+contIdInput).value > 0){
			document.getElementById('nombreArticulo_'+contIdInput).value = '';
			document.getElementById('idArticulo_'+contIdInput).value     = '';
			document.getElementById('costoArticulo_'+contIdInput).value  = '';
			document.getElementById('unidades_'+contIdInput).value       = '';

			document.getElementById('divGuardarNewArticuloOrdenCompra_'+contIdInput).style.display    = 'block';
			document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+contIdInput).style.display = 'block';
		}
		else if(document.getElementById('idArticulo_'+contIdInput).value > 0){
			document.getElementById('nombreArticulo_'+contIdInput).value = '';
			document.getElementById('idArticulo_'+contIdInput).value     = '';
			document.getElementById('costoArticulo_'+contIdInput).value  = '';
			document.getElementById('unidades_'+contIdInput).value       = '';
		}
		return true;
	}

	function ajaxBuscarArticuloOrdenCompra(valor,input){
		var arrayIdInput = input.split('_');
		Ext.get('renderArticuloOrdenCompra_'+arrayIdInput[1]).load({
			url		: 'ordenes_compra/bd/bd.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc           : 'buscarArticuloOrdenCompra',
				valorArticulo : valor,
				contArticulo    : arrayIdInput[1],
			}
		});
	}

	//======================== VENTANA BUSCAR ARTICULO POR PORVEEDOR =========================//
	function ventanaBuscarArticuloOrdenCompra(cont){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();
		var sql     = 'AND estado_compra = "true"';

		Win_Ventana_buscar_Articulo_orden_compra = new Ext.Window({
			width       : myancho-100,
            height      : myalto-50,
			id			: 'Win_Ventana_buscar_Articulo_orden_compra',
			title		: 'Articulos Proveedor',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		: '../funciones_globales/grillas/BusquedaInventarios.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					sql           : sql,
					nombre_grilla : 'ventanaBucarArticuloOrdenCompra',
					nombreTabla   : 'items',
					cargaFuncion  : 'responseVentanaBuscarArticuloOrdenCompra(id,'+cont+');'
				}
			},
			tbar		:
			[
				{
					xtype     : 'button',
					width     : 60,
					height    : 56,
					text      : 'Regresar',
					scale     : 'large',
					iconCls   : 'regresar',
					iconAlign : 'top',
					handler   : function(){ Win_Ventana_buscar_Articulo_orden_compra.close(id) }
				},'-'
			]
		}).show();
	}

	function responseVentanaBuscarArticuloOrdenCompra(id,cont){
		document.getElementById('cantArticulo_'+cont).focus();

		var codigo          = document.getElementById('div_ventanaBucarArticuloOrdenCompra_codigo_'+id).innerHTML;
		var costo           = document.getElementById('div_ventanaBucarArticuloOrdenCompra_costos_'+id).innerHTML;
		var unidadMedida    = document.getElementById('unidad_medida_grilla_'+id).innerHTML;
		var nombreArticulo  = document.getElementById('div_ventanaBucarArticuloOrdenCompra_nombre_equipo_'+id).innerHTML;

		Ext.get('renderArticuloOrdenCompra_'+cont).load({
                url     : 'ordenes_compra/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opc           : 'cargarIvaArticuloOrdenCompra',
					id_inventario : id,
					cont          : cont

                }
            });

		if(document.getElementById('idInsertArticulo_'+cont).value > 0){ document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+cont).style.display = 'block'; }

		document.getElementById('unidades_'+cont).value       = unidadMedida;
		document.getElementById('idArticulo_'+cont).value     = id;
		document.getElementById('eanArticulo_'+cont).value    = codigo;
		document.getElementById('costoArticulo_'+cont).value  = costo;
		document.getElementById('nombreArticulo_'+cont).value = nombreArticulo;

		document.getElementById('divGuardarNewArticuloOrdenCompra_'+cont).style.display = 'block';
		Win_Ventana_buscar_Articulo_orden_compra.close();
	}

	//=========================== ELIMINAR ARTICULO ==========================//
	function deleteArticuloOrdenCompra(cont){
		var idArticulo = document.getElementById('idInsertArticulo_'+cont).value;

		//datos para recalcular los valores de la factura
		var cantArticulo      = document.getElementById('cantArticulo_'+cont).value;
		var descuentoArticulo = document.getElementById('descuentoArticulo_'+cont).value;
		var costoArticulo     = document.getElementById('costoArticulo_'+cont).value;
		var iva 			  = document.getElementById('ivaArticuloOrdenCompra_'+cont).value;
		var accion 			  = 'eliminar';
		var tipoDescuento     = ((document.getElementById('imgDescuentoArticuloOrdenCompra_'+cont).getAttribute("src")).split('/')[1]).split('.')[0];

		if(confirm('Esta Seguro de eliminar este articulo de la orden de compra?')){
			Ext.get('renderArticuloOrdenCompra_'+cont).load({
				url		: 'ordenes_compra/bd/bd.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc           : 'deleteArticuloOrdenCompra',
					idArticulo    : idArticulo,
					cont          : cont,
					idOrdenCompra : '<?php echo $id_orden_compra; ?>'
				}
			});

			//llamamos la funcion que calcula los totales de la orden de compra
			calcularValoresOrdenCompra(cantArticulo,descuentoArticulo,costoArticulo,accion,tipoDescuento,iva,cont)
		}
	}

	//====================== CANCELAR UPDATE EN ARTICULO ========================//
	function noUpdateArticuloOrdenCompra(cont){
        var id_articulo = document.getElementById("idInsertArticulo_"+cont).value;
        Ext.get('renderArticuloOrdenCompra_'+cont).load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc           : 'noUpdateArticuloOrdenCompra',
				cont          : cont,
				idArticulo    : id_articulo,
				idOrdenCompra : '<?php echo $id_orden_compra; ?>'
            }
        });
    }

    //========================= FUNCION PARA CALCULAR LOS TOTALES DE LA ORDEN DE COMPRA ==============================//
   	/*
	    ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
	    ->descuento     =  {si es en porcentaje = (subtotal * descuento) /100
	                       {si es en pesos = (subtotal-descuento)

	    ->iva           = ((iva)*(subtotal-descuento))/100
	    ->retefuente    = ((retefuente)*(subtotal-descuento))/100
    	->total         = (subtotal-descuento) + iva + retefuente
    */

    function calcularValoresOrdenCompra(cantidad,descuento,costo,accion,tipoDesc,iva,cont){
    	if(!document.getElementById('contenedor_totales_ordenes_compras')){ return; }

		var subtotal         = 0
		, 	valor_iva        = 0
		,	descuentoTotal   = 0
		,	descuentoMostrar = 0;

		subtotal=(cantidad*costo);

		if (tipoDesc=='porcentaje') { subtotal = subtotal-(subtotal*descuento/100); }	// DESCUENTO POR ORDEN
		else if(tipoDesc=='pesos'){ subtotal = subtotal-descuento; }

		if (iva >0) {
            valor_iva = (parseFloat(arrayIvaOrdenCompra[iva].valor)*parseFloat(subtotal))/100; //IVA NETO
        }
        else{
			valor_iva = 0; //IVA NETO
			iva       = 0;
        }
        // console.log("array: "+arrayIvaOrdenCompra[iva].valor+" * subtotal: "+subtotal);
		if (accion=='agregar') {
			// valor_iva = (parseFloat(iva)*parseFloat(subtotal))/100;

			subtotalOrdenCompra = (parseFloat(subtotal)+parseFloat(subtotalOrdenCompra));			// ACUMULADOR SUBTOTAL
			ivaOrdenCompra      = parseFloat(ivaOrdenCompra)+parseFloat(valor_iva );				// ACUMULADOR IVA

			//SI EL OBJETO SALDO EN EL ARRAY DEL IVA NO EXISTE, CREAR EL CAMPO SALDO CON EL PRIMER VALOR
            if (typeof(arrayIvaOrdenCompra[iva].saldo)=='undefined') {
                arrayIvaOrdenCompra[iva].saldo=valor_iva;
            }
            //SI YA EXISTE EL CAMPO SALDO EN EL OBJETO, ENTONCES ACUMULAR EL VALOR
            else{
                arrayIvaOrdenCompra[iva].saldo=arrayIvaOrdenCompra[iva].saldo+valor_iva;
            }

		}
		else if (accion=='eliminar') {
			// valor_iva =(parseFloat(iva)*parseFloat(subtotal))/100;

			subtotalOrdenCompra = parseFloat(subtotalOrdenCompra) - parseFloat(subtotal);			// ACUMULADOR SUBTOTAL
			ivaOrdenCompra      = parseFloat(ivaOrdenCompra)-parseFloat(valor_iva );				// ACUMULADOR IVA

			//SI EL OBJETO SALDO EN EL ARRAY DEL IVA EXISTE, RESTAR EL VALOR DEL IVA
            if (typeof(arrayIvaOrdenCompra[iva].saldo)!='undefined') {
                arrayIvaOrdenCompra[iva].saldo-=valor_iva;
            }

		}

		//RECORRER EL ARRAY DE LOS IVA Y ARMAR ELEMENTOS PARA EL DOM
        var labelIva   = ''
        ,   simboloIva = ''
        ,   valoresIva = '';

        for(var id_iva in arrayIvaOrdenCompra){
            // console.log(arrayIvaOrdenCompra[id_iva].nombre+' - '+arrayIvaOrdenCompra[id_iva].valor+' - '+arrayIvaOrdenCompra[id_iva].saldo);
            if (typeof(arrayIvaOrdenCompra[id_iva].saldo)!='undefined') {
                if (arrayIvaOrdenCompra[id_iva].saldo>0) {
                    // console.log(arrayIvaOrdenCompra[id_iva].saldo);
                    labelIva+='<div style=\"margin-bottom:5px; overflow:hidden; width:100%; padding-left:3px; font-weight:bold; overflow:hidden;margin-bottom:5px;\"><div class=\"labelNombreRetencion\">'+arrayIvaOrdenCompra[id_iva].nombre+'</div><div class=\"labelValorRetencion\">('+(arrayIvaOrdenCompra[id_iva].valor*1)+'%)</div></div>';
                    simboloIva+='<div style=\"margin-bottom:5px\">$</div>';
                    valoresIva+='<div style=\"margin-bottom:5px\" title=\"'+formato_numero(arrayIvaOrdenCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA']; ?>", '.', ',')+'\" >'+formato_numero(arrayIvaOrdenCompra[id_iva].saldo, "<?php echo $_SESSION['DECIMALESMONEDA'] ?>", '.', ',')+'</div>';
                }
            }
        }

		//MOSTRAR EL TOTAL INDIVIDUAL
		document.getElementById('costoTotalArticuloOrdenCompra_'+cont).value=subtotal.toFixed(2);

		totalOrdenCompra=parseFloat(subtotalOrdenCompra)+parseFloat(ivaOrdenCompra);

		document.getElementById("subtotalOrdenCompra").innerHTML   = formato_numero(subtotalOrdenCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
		// document.getElementById("ivaOrdenCompra").innerHTML     = formato_numero(ivaOrdenCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
		document.getElementById('labelIvaOrdenCompra').innerHTML   = labelIva;
		document.getElementById('simboloIvaOrdenCompra').innerHTML = simboloIva;
		document.getElementById('ivaOrdenCompra').innerHTML        = valoresIva;

		document.getElementById("totalOrdenCompra").innerHTML      = formato_numero(totalOrdenCompra,<?php echo $_SESSION['DECIMALESMONEDA'] ?>,'.',',');
    }

    function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero = parseFloat(numero);
        if(isNaN(numero)){ return ''; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); } // Redondeamos


        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) {
                numero=numero.replace(miles, '$1' + separador_miles + '$2');
            }
        }

        return numero;
    }

	//================================== VALIDACION CAMPOS NUMERICOS ===========================================//
	function validarNumberArticuloOrdenCompra(event,input,typeValidate){
		var idInput     = (input.id).split('_')[0];
		var contIdInput = (input.id).split('_')[1];

		numero = input.value;
		tecla  = (input) ? event.keyCode : event.which;

		if(tecla == 13){
			if(idInput == 'cantArticulo'){ document.getElementById('descuentoArticulo_'+contIdInput).focus(); }
			else if(idInput == 'descuentoArticulo'){ document.getElementById('costoArticulo_'+contIdInput).focus(); }
			return true;
		}
		else if(tecla == 9 || tecla == 37 || tecla == 38 || tecla == 39 || tecla == 40 || tecla == 18 || tecla == 17 || tecla == 16){ return true; }

		patron = /[^\d.]/g;
        if(patron.test(numero)){
			numero      = numero.replace(patron,'');
			input.value = numero;
        }
        else if(isNaN(numero)){ input.value = numero.substring(0, numero.length-1); }
	    else{
			document.getElementById('divGuardarNewArticuloOrdenCompra_'+contIdInput).style.display    = 'block';
			if(document.getElementById('idInsertArticulo_'+contIdInput).value > 0){
				document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+contIdInput).style.display = 'block';
			}
		}
	    return true;
	}

	//======================== VENTANA OBSERVACION POR ARTICULO EN ORDEN DE COMPRA =============================//
	function ventanaDescripcionArticuloOrdenCompra(cont){
		var idArticulo = document.getElementById('idArticulo_'+cont).value
		,   idInsert = document.getElementById('idInsertArticulo_'+cont).value;
		Win_Ventana_descripcion_Articulo_orden_compra = new Ext.Window({
			width		: 350,
			height		: 350,
			id			: 'Win_Ventana_descripcion_Articulo_orden_compra',
			title		: 'Observacion articulo ',
			modal		: true,
			autoScroll	: false,
			closable	: false,
			autoDestroy : true,
			autoLoad	:
			{
				url		: 'ordenes_compra/bd/bd.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc           : 'ventanaDescripcionArticulo',
					cont          : cont,
					idArticulo    : idInsert,
					idOrdenCompra : '<?php echo $id_orden_compra; ?>',
				}
			},
			tbar		:
			[
				{
					xtype		: 'button',
					width		: 60,
					height		: 56,
					text		: 'Guardar',
					scale		: 'large',
					iconCls		: 'guardar',
					iconAlign	: 'top',
					handler 	: function(){ btnGuardarDescripcionArticuloOrdenCompra(cont,idArticulo,idInsert); }
				},
				{
					xtype		: 'button',
					width		: 60,
					height		: 56,
					text		: 'Regresar',
					scale		: 'large',
					iconCls		: 'regresar',
					iconAlign	: 'top',
					handler 	: function(){ Win_Ventana_descripcion_Articulo_orden_compra.close(id) }
				}
			]
		}).show();
	}

	function btnGuardarDescripcionArticuloOrdenCompra(cont,idArticulo,idInsert){
		var idCentroCostos = document.getElementById("id_ccos_oc").value
        ,   observacion    = document.getElementById("observacionArticuloOrdenCompra_"+cont).value
        ,	idImpuesto     = document.getElementById("id_impuestoItem_oc").value;

        observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

        Ext.get('renderizaGuardarObservacionOrden_'+cont).load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc            : 'guardarDescripcionArticuloOrdenCompra',
				cont           : cont,
				idInventario   : idArticulo,
				idInsert       : idInsert,
				idCentroCostos : idCentroCostos,
				observacion    : observacion,
				idOrdenCompra  : '<?php echo $id_orden_compra; ?>',
				id_impuesto    : idImpuesto,
				filtro_bodega  : document.getElementById("filtro_ubicacion_ordenes_compra").value
            }
        });
	}

	//================================= BOTON GUARDAR ORDEN DE COMPRA ====================================//
	function guardarOrdenCompra(){
		var validacion  = validarArticulosOrdenCompra()
        ,   observacion = document.getElementById("observacionOrdenCompra").value;

        if (validacion==0) { alert("No hay articulos por guardar en la presente orden de compra!"); return; }
        else if (validacion==1) { alert("Hay articulos pendientes por guardar!"); return;}
        else if (validacion== 2 || validacion== 0) {


            observacion = observacion.replace(/[\#\<\>\'\"]/g, '');
            cargando_documentos('Generando Documento...','');
			Ext.get('render_btn_guardar').load({
				url		: 'ordenes_compra/bd/bd.php',
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc           : 'btnTerminarOrdenCompra',
					idOrdenCompra : '<?php echo $id_orden_compra; ?>',
				}
			});
		}
	}

	//=========================== BOTON VENTANA BUSCAR ORDEN DE COMPRA ==============================//
	function ventanaBuscarOrdenCompra(){
		var myalto  = Ext.getBody().getHeight();
		var myancho = Ext.getBody().getWidth();

		Win_Ventana_buscar_orden_compra = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_orden_compra',
            title       : 'Seleccionar Orden de compra',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'ordenes_compra/grilla_buscar_orden_compra.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opc           : 'buscar_orden_compra',
					filtro_bodega : document.getElementById("filtro_ubicacion_ordenes_compra").value
                }
            },
            tbar        :
            [
                {
                    xtype       : 'button',
                    width		: 60,
					height		: 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_orden_compra.close(id) }
                },'-'
            ]
        }).show();
	}

	function buscarOrdenCompra(){
		var validacion= validarArticulosOrdenCompra();
		if (validacion==1) {
            if(confirm("Aviso!\nHay articulos pendientes por guardar!\nLa informacion no guardada se perdera\nRealmente desea continuar?")){ ventanaBuscarOrdenCompra(); }
        }
        else if (validacion== 2 || validacion== 0) { ventanaBuscarOrdenCompra(); }
	}

	//=========================== CAMBIA TIPO DE DESCUENTO POR ARTICULO ==============================//
	function tipoDescuentoArticuloOrdenCompra(cont,divImg){
		document.getElementById('divGuardarNewArticuloOrdenCompra_'+cont).style.display    = 'block';
		// Si existe un articulo almacenado muestra el boton deshacer
		if(document.getElementById('idInsertArticulo_'+cont).value > 0){
			document.getElementById('divImageNoUpdateArticuloOrdenCompra_'+cont).style.display = 'block';
		}

		 //si esta en signo porcentaje cambia a pesos, y viceversa
        if (divImg.getAttribute('title') == 'En porcentaje') {
            divImg.setAttribute("title","En pesos");
            document.getElementById('imgDescuentoArticuloOrdenCompra_'+cont).setAttribute("src","img/pesos.png");
            document.getElementById('descuentoArticulo_'+cont).focus();
        }
        else if(divImg.getAttribute('title') == 'En pesos'){
        	divImg.setAttribute("title","En porcentaje");
            document.getElementById('imgDescuentoArticuloOrdenCompra_'+cont).setAttribute("src","img/porcentaje.png");
            document.getElementById('descuentoArticulo_'+cont).focus();
        }
	}


	//====================== GUARDAR LA OBSERVACION GENERAL DE LA ORDEN DE COMPRA ==================//
	function inputObservacionOrdenCompra(event,input){
        document.getElementById('labelObservacionOrdenCompra').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;margin-right:10px;"><img src="../../temas/clasico/images/loading.gif" ></div>';
    	tecla  = (input) ? event.keyCode : event.which;
    	if(tecla == 13 || tecla == 9){
    		guardarObservacionOrdenCompra();
    	}

    	clearTimeout(timeOutObservacionOrdenCompra);
    	timeOutObservacionOrdenCompra = setTimeout(function(){
    		guardarObservacionOrdenCompra();
    	},1500);
	}

	function guardarObservacionOrdenCompra(){
		var observacion = document.getElementById('observacionOrdenCompra').value;
		observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

		clearTimeout(timeOutObservacionOrdenCompra);
		timeOutObservacionOrdenCompra = '';

		Ext.Ajax.request({
			url		: 'ordenes_compra/bd/bd.php',
			params	:
			{
				opc           : 'guardarObservacionOrdenCompra',
				idOrdenCompra : '<?php echo $id_orden_compra; ?>',
				observacion   : observacion
			},
			success	:function (result, request){
						if(result.responseText != 'true'){
							document.getElementById("observacionOrdenCompra").value=observacionOrdenCompra;
                            document.getElementById('labelObservacionOrdenCompra').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 1</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionOrdenCompra').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);

							/*alert('No hay conexion con el servidor,\nPor favor intentelo de nuevo si el problema persiste comuniquese con el administrador del sistema');
							document.getElementById("observacionOrdenCompra").value=observacionOrdenCompra;*/
						}
						else{

							observacionOrdenCompra=observacion;
                            document.getElementById('labelObservacionOrdenCompra').innerHTML='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Guardado</div>';
                            setTimeout(function () {
                                document.getElementById('labelObservacionOrdenCompra').innerHTML='<b>OBSERVACIONES</b>';
                            },1200);

						}
					},
			failure : function(){
						document.getElementById("observacionOrdenCompra").value          =observacionOrdenCompra;
						document.getElementById('labelObservacionOrdenCompra').innerHTML ='<b>OBSERVACIONES</b> <div style="text-align: center;float:right;margin-top:3px;font-weight:bold;font-style:italic;color:#999;margin-right:10px;">Error 2</div>';
							setTimeout(function () {
								document.getElementById('labelObservacionOrdenCompra').innerHTML ='<b>OBSERVACIONES</b>';
							},1200);
					}
		});

	}

	//================================= IMPRIMIR ORDEN DE COMPRA ====================================//
	function imprimirOrdenCompra(){
		window.open("ordenes_compra/imprimir_orden_compra.php?id="+'<?php echo $id_orden_compra; ?>');
	}

	//================================ ELIMINA ORDENES DE COMPRA ====================================//
	function btnDeleteOrdenCompra(){
		var contArticulos = 0;
		if(!document.getElementById('DivArticulos')){ return; }

		arrayIdsArticulos = document.getElementById('DivArticulos').querySelectorAll('.classInputInsertArticulo');
		for(i in arrayIdsArticulos){
			if(arrayIdsArticulos[i].value > 0){ contArticulos++; }
		}

		if(contArticulos > 0){
			if(confirm('Esta seguro de Eliminar la presente Orden de compra y su contenido relacionado')){
                cargando_documentos('Cancelando Documento...','');
				Ext.get('render_btn_guardar').load({
					url		: 'ordenes_compra/bd/bd.php',
					scripts	: true,
					nocache	: true,
					params	:
					{
						opc           : 'btnEliminarOrdenCompra',
						idOrdenCompra : '<?php echo $id_orden_compra; ?>',
                        filtro_bodega : document.getElementById("filtro_ubicacion_ordenes_compra").value
					}
				});
			};
		}
	}

	//================================// EXPORTAR ORDEN EN EXCEL //================================//
	function imprimirOrdenCompraExcel(){
        window.open("ordenes_compra/exportar_excel_orden_compra.php?id=<?php echo $id_orden_compra; ?>");
    }

    //===============================// IMPRIMIR FACTURA DE COMPRA //===============================//
    function imprimirFacturaCompra (){
        window.open("facturacion/imprimir_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    function imprimirFacturaCompraExcel (){
        window.open("facturacion/exportar_excel_factura_compra.php?id="+'<?php echo $id_factura_compra; ?>');
    }

    //====================// VALIDAR QUE NO HAYA NINGUN ARTICULO POR GUARDAR O POR ACTULIZAR //====================//
    function validarArticulosOrdenCompra(){
        var cont = 0
		, 	contTotal = 0
		,	contArticulo
		,	nameArticulo
		,	divsArticulos = document.querySelectorAll(".bodyDivArticulos");

		for(i in divsArticulos){
			if(typeof(divsArticulos[i].id)!='undefined'){
				contTotal++;
				nameArticulo = (divsArticulos[i].id).split('_')[0]
				contArticulo = (divsArticulos[i].id).split('_')[1]
				if(		document.getElementById('idArticulo_'+contArticulo).value > 0
					&& 	document.getElementById('imgSaveOrdenCompra_'+contArticulo).getAttribute('src') == 'img/save_true.png'
					|| 	document.getElementById('imgSaveOrdenCompra_'+contArticulo).getAttribute('src') == 'img/reload.png'
					&&  document.getElementById('divGuardarNewArticuloOrdenCompra_'+contArticulo).style.display == 'block')
					{ cont++; }
			}
		}

        if(contTotal==1 || contTotal==0){  return 0; }		// si no se han almacenado articulos retornamos 0
        else if(cont > 0){ return 1; }		// si hay articulos pendientes por guardar o actualizar retornamos 1
        else { return 2; }					// si toda la validacion esta bien retornamos 2
    }

    function ventana_centros_costos_oc(){
        Win_Ventana_Ccos_oc = new Ext.Window({
            width       : 600,
            height      : 450,
            id          : 'Win_Ventana_Ccos_oc',
            title       : 'Seleccione el Centro de Costo',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'ordenes_compra/centro_costos.php',
                scripts : true,
                nocache : true,
                params  : { }
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
                            iconAlign   : 'top',
                            handler     : function(){ Win_Ventana_Ccos_oc.close() }
                        }
                    ]
                }
            ]
        }).show();
    }

    function UpdateOrdenCompra(valor){

       Ext.get('renderSelectTipoOrden').load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
				opc      : 'UpdateTipoOrden',
				id_orden : '<?php echo $id_orden_compra; ?>',
				id_tipo  : valor
            }
        });
    }

    //=========================== VENTANA BUSCAR VENDEDOR ======================================//
    function buscarVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(){

        Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?> = new Ext.Window({
            width       : 550,
            height      : 400,
            id          : 'Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>',
            title       : 'Departamentos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../funciones_globales/grillas/grillaBuscarDepartamentos.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    cargaFunction : 'renderizaResultadoVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(id);',
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
                    handler     : function(){ Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>.close(id) }
                }
            ]
        }).show();
    }

    function renderizaResultadoVentanaAreaSolicitante<?php echo $opcGrillaContable; ?>(id){
        var departamento = document.getElementById('div_costo_departamentos_nombre_'+id).innerHTML;
        var codigo = document.getElementById('div_costo_departamentos_codigo_'+id).innerHTML;
        //mostramos el nombre del vendedor en el campo
        document.getElementById("areaSolcitante<?php echo $opcGrillaContable; ?>").value = departamento;
        ajaxGuardarAreaSolicitante<?php echo $opcGrillaContable; ?>(id,codigo,departamento);

        Win_Ventana_area_solicitante_<?php echo $opcGrillaContable; ?>.close();
    }

    function ajaxGuardarAreaSolicitante<?php echo $opcGrillaContable; ?>(id,codigo,departamento){

        Ext.get('loadAreaSolicitante').load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                           : 'guardarAreaSolicitante',
                id                            : '<?php echo $id_orden_compra; ?>',
                id_area_solicitante           : id,
                codigo_area_solicitante       : codigo,
                departamento_area_solicitante : departamento,
            }
        });
    }

    //======================== VENTANA BUSCAR PEDIDO - COTIZACION ===========================================================//
    function ventanaBuscarDocumentoCruceOrdenCompra(){


        titulo         = "Seleccione la Requisicion";
        tablaGrilla    = "compras_requisicion";
        nameGrillaLoad = "grillaRequisicionEntradaAlmacen";


        var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_documento_cruceOrdenCompra = new Ext.Window({
            width       : myancho-100,
            height      : myalto-50,
            id          : 'Win_Ventana_buscar_documento_cruceOrdenCompra',
            title       : titulo,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'ordenes_compra/bd/grillaBuscarDocumentoCruce.php',
                scripts : true,
                nocache : true,
                params  :
                {
                    opc                 : 'buscar_documento_cruce',
                    id_documento        : '<?php echo $id_orden_compra; ?>',
                    opcGrillaContable   : 'OrdenCompra',
                    tablaDocumentoCruce : tablaGrilla,
                    nameGrillaLoad      : nameGrillaLoad,
                    filtro_bodega       : document.getElementById("filtro_ubicacion_ordenes_compra").value
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
                            handler     : function(){ Win_Ventana_buscar_documento_cruceOrdenCompra.close(id) }
                        }
                    ]
                }
            ]
        }).show();
    }

</script>

