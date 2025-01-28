<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];
	$bodyArticle = '';
	$acumScript  = '';
	$descuento   = 0;
	$divImagen   = '';
?>
<script>
	var contArticulosOrdenCompra = 1
	,	codigoProveedor          = 0
	,	nitProveedor             = 0
	,	nombreProveedor          = '';

	//variables para calcular los valores de los costos y totales de la factura
	var subtotalOrdenCompra                   = 0.00
	,   acumuladodescuentoArticuloOrdenCompra = 0.00
	,   ivaOrdenCompra                        = 0.00
	,   ivaOrdenCompra1                       = 0.00
	,   retefuenteOrdenCompra                 = 0.00
	,   retefuenteOrdenCompra2                = 0.00
	,   totalOrdenCompra                      = 0.00;

	Ext.getCmp("Btn_imprimir_orden_compra").enable();
	Ext.getCmp("Btn_guardar_orden_compra").disable();
	Ext.getCmp("Btn_editar_orden_compra").disable();
	Ext.getCmp("Btn_cancelar_orden_compra").disable();
	Ext.getCmp("Btn_restaurar_orden_compra").disable();
	Ext.getCmp("BtnGroup_Estado1_orden_compra").show();
  Ext.getCmp("BtnGroup_Guardar_orden_compra").hide();
</script>
<?php

	include("bd/functions_body_article.php");

	$sql = "SELECT
						id_proveedor,
						nit,
						cod_proveedor,
						proveedor,
						estado,
						observacion,
						fecha_inicio,
						fecha_vencimiento,
						tipo_nombre,
						forma_pago,
						area_solicitante
					FROM compras_ordenes
					WHERE id = '$id_orden_compra' AND activo = 1";
	$query = mysql_query($sql,$link);

	$nit               = mysql_result($query,0,'nit');
	$estado            = mysql_result($query,0,'estado');
	$proveedor         = mysql_result($query,0,'proveedor');
	$id_proveedor      = mysql_result($query,0,'id_proveedor');
	$cod_proveedor     = mysql_result($query,0,'cod_proveedor');
	$fecha_inicio      = mysql_result($query,0,'fecha_inicio');
	$fecha_vencimiento = mysql_result($query,0,'fecha_vencimiento');
	$tipo_nombre       = mysql_result($query,0,'tipo_nombre');
	$forma_pago        = mysql_result($query,0,'forma_pago');
	$area_solicitante  = mysql_result($query,0,'area_solicitante');

	$user_permiso_generar   = (user_permisos(33,'false') == 'true')? 'Ext.getCmp("Btn_guardar_orden_compra").enable();' : ''; 		//guardar
	$user_permiso_editar    = (user_permisos(34,'false') == 'true')? 'Ext.getCmp("Btn_editar_orden_compra").enable();' : ''; 		//editar
	$user_permiso_cancelar  = (user_permisos(35,'false') == 'true')? 'Ext.getCmp("Btn_cancelar_orden_compra").enable();' : ''; 		//calcelar
	$user_permiso_restaurar = (user_permisos(36,'false') == 'true')? 'Ext.getCmp("Btn_restaurar_orden_compra").enable();Ext.getCmp("Btn_imprimir_orden_compra").disable(); setTimeout(function(){Ext.getCmp("Btn_cancelar_orden_compra").disable();},1500); ' : ''; 	//restaurar

	if ($estado == 1) { $acumScript .= $user_permiso_editar.$user_permiso_cancelar.'Ext.getCmp("Btn_imprimir_orden_compra").enable();'; }			//documento generado
	else if($estado == 2){  $divImagen ='<img src="img/candado44.png" style="float:right; width: 20px; height: 30px; margin:10px 0 10px 2px;" title="Orden de compra Cruzada">'; $acumScript .= 'Ext.getCmp("Btn_imprimir_orden_compra").enable();'.$user_permiso_editar.$user_permiso_cancelar;} 	//documento cruzado con otro
	else if($estado == 3){ $acumScript .= $user_permiso_restaurar.'Ext.getCmp("Btn_imprimir_orden_compra").enable();'; }		//documento cancelado

	$arrayReplaceString     = array("\n", "\r","<br>");
	$observacionOrdenCompra = str_replace($arrayReplaceString, "\\n", mysql_result($query,0,'observacion'));

	$acumScript .= 'document.getElementById("nitProveedor").value   											= "'.$nit.'";
									document.getElementById("codProveedor").value                         = "'.$cod_proveedor.'";
									document.getElementById("nombreProveedor").value                      = "'.$proveedor.'";
									document.getElementById("fechaInicioOrdenCompra").value               = "'.$fecha_inicio.'";
									document.getElementById("fechaVencimientoOrdenCompra").value          = "'.$fecha_vencimiento.'";
									document.getElementById("forma_pago").value                           = "'.$forma_pago.'";
									document.getElementById("tipoOrden").value                            = "'.$tipo_nombre.'";
									document.getElementById("areaSolcitante'.$opcGrillaContable.'").value = "'.$area_solicitante.'";

									id_proveedor_orden_compra = "'.$id_proveedor.'";
									codigoProveedor           = "'.$cod_proveedor.'";
									nitProveedor              = "'.$nit.'";
									nombreProveedor           = "'.$proveedor.'";';

	//CUANTAS FACTURAS DE COMPRA
    $acumFacturasCompra = '';
    $margin_left        = '';
    $sqlOrdenesCompra   = "SELECT DISTINCT CF.id, CF.numero_factura, CF.prefijo_factura
    						FROM compras_facturas AS CF,
    							compras_facturas_inventario AS CFI
    						WHERE CFI.activo=1
    							AND CFI.id_factura_compra=CF.id
    							AND CFI.id_consecutivo_referencia='$id_orden_compra'
    							AND CFI.nombre_consecutivo_referencia='Orden de Compra'
    							AND CF.numero_factura <> ''";
    $queryOrdenesCompra = mysql_query($sqlOrdenesCompra,$link);

    while($rowOrdenesCompra = mysql_fetch_array($queryOrdenesCompra)){
    	$prefijo_factura = ($rowOrdenesCompra['prefijo_factura'] != '')? $rowOrdenesCompra['prefijo_factura'].' ': '';
        $acumFacturasCompra .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;">
                                <div class="contenedorInputOrdenCompraFactura">
                                    <input type="text" class="inputOrdenCompraFactura" value="'.$prefijo_factura.' '.$rowOrdenesCompra['numero_factura'].'" readonly/>
                                </div>
                            </div>';
        $margin_left = 'margin-left:5px';
    }
    $bodyArticle = cargaArticulosOrdenCompraSave($id_orden_compra,$observacionOrdenCompra,2,$link);


    $acumDocReferencia  = '';
    $margin_left        = 'margin-left:5px';
    $sqlDocReferencia   = "SELECT DISTINCT id_consecutivo_referencia AS id_referencia, consecutivo_referencia AS cod_referencia, LEFT(nombre_consecutivo_referencia,1) AS doc_referencia
                            FROM compras_ordenes_inventario
                            WHERE id_consecutivo_referencia>0 AND id_orden_compra='$id_orden_compra' AND activo=1
                            ORDER BY id ASC";
    $queryDocReferencia = mysql_query($sqlDocReferencia,$link);

    while($rowDocReferencia = mysql_fetch_array($queryDocReferencia)){

        if($rowDocReferencia['doc_referencia'] == 'R'){ $title = 'Eliminar los Articulos de la Requisicion'; }
        else if($rowDocReferencia['doc_referencia'] == 'O'){ $title = 'Eliminar los Articulos de la Orden de Compra'; }

        $typeDocCruce   = $rowDocReferencia['doc_referencia'];
        $numeroDocCruce = $rowDocReferencia['consecutivo_referencia'];

        $acumDocReferencia .='<div style="width:136px; '.$margin_left.'; float:left; overflow:hidden;height: 22px;" id="divDocReferencia'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                    <div class="contenedorInputDocReferenciaFactura">
                                        <input type="text" class="inputDocReferenciaFactura" value="'.$rowDocReferencia['doc_referencia'].' '.$rowDocReferencia['cod_referencia'].'" style="border-bottom: 1px solid #d4d4d4;" readonly/>
                                    </div>
                                    <div title="'.$title.' # '.$rowDocReferencia['cod_referencia'].' en la presente entrada de almacen" onclick="eliminaDocReferencia'.$opcGrillaContable.'(\''.$rowDocReferencia['id_referencia'].'\',\''.$rowDocReferencia['doc_referencia'].'\',\''.$id_documento.'\')" style="float:left; width:18px; height:18px; margin:1px 0 0 -22px; background-image: url(img/MyGrillaFondo.png); border: 1px solid #d4d4d4;display:none">
                                        <div style="overflow:hidden; border-radius:35px; height:16px; width:16px; margin:1px; font-size:12px;" id="btn'.$opcGrillaContable.'_'.$rowDocReferencia['doc_referencia'].'_'.$rowDocReferencia['id_referencia'].'">
                                            <div style="width:7px; height:2px; background-color:#fff; margin:7px 4px;"></div>
                                        </div>
                                    </div>
                              </div>';
    }


?>

<div class="contenedorOrdenCompra">

	<!-- Campo Izquierdo -->
	<div class="bodyTop">
		<div class="contInfoFact">
			<div id="render_btns_orden_compra"></div>
			<div class="contTopFila">
				<?php echo $divImagen; ?>
				<div class="renglonTop">
                    <div class="labelTop">Factura de Compra</div>
                    <div class="campoTop"><?php echo $acumFacturasCompra; ?></div>
                </div>
                <div class="renglonTop">
					<div class="labelTop">Fecha Inicio</div>
					<div class="campoTop"><input type="text" id="fechaInicioOrdenCompra" Readonly/></div>
				</div>
				<div class="renglonTop">
					<div class="labelTop">Fecha Vencimiento</div>
					<div class="campoTop"><input type="text" id="fechaVencimientoOrdenCompra" Readonly/></div>
				</div>
				<div class="renglonTop">
                    <div class="labelTop">Forma de pago</div>
                    <div class="campoTop"><input type="text" id="forma_pago" Readonly/></div>
                </div>
				<div class="renglonTop">
                    <div class="labelTop">Docs. Cruce</div>
                    <div class="campoTop" style="height:auto;"  id="contenedorDocsReferencia<?php echo $opcGrillaContable; ?>"><?php echo $acumDocReferencia; ?></div>
                </div>
				<div class="renglonTop">
					<div class="labelTop">Codigo Proveedor</div>
					<div class="campoTop"><input type="text" id="codProveedor" Readonly/></div>
				</div>
				<div class="renglonTop">
					<div class="labelTop">Nit</div>
					<div class="campoTop"><input type="text" id="nitProveedor" Readonly/></div>
				</div>
				<div class="renglonTop">
					<div class="labelTop">Proveedor</div>
					<div class="campoTop" style="width:277px"><input type="text" id="nombreProveedor" Readonly/></div>
				</div>
				<div class="renglonTop">
					<div class="labelTop">Tipo</div>
					<div class="campoTop"><input type="text" id="tipoOrden" Readonly/></div>
				</div>
				<div class="renglonTop">
                    <div class="labelTop">Area o Departamento Solicitante</div>
                    <div class="campoTop" style="width:277px;"><input type="text" id="areaSolcitante<?php echo $opcGrillaContable; ?>" style="width:100%" readonly value="<?php echo $area_solicitante; ?>"/></div>
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

	<?php echo $acumScript; ?>

    //========================= FUNCION PARA CALCULAR LOS TOTALES DE LA FACTURA ==============================//
   	/*
	    ->subtotal      = (suma de (cantidad * costo) de cada uno de los articulos)
	    ->iva           = ((iva)*(subtotal-descuento))/100
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

		if (iva != '') {
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

	//=========================== BOTON VENTANA BUSCAR ORDEN DE COMPRA ==============================//
	function buscarOrdenCompra(){ ventanaBuscarOrdenCompra(); }

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
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Opciones',
                    items   :
                    [
                        {
                            xtype       : 'button',
                            width		    : 60,
														height	  	: 56,
                            text        : 'Regresar',
                            scale       : 'large',
                            iconCls     : 'regresar',
                            iconAlign   : 'left',
                            handler     : function(){ Win_Ventana_buscar_orden_compra.close(id) }
                        }
                    ]
                }
            ]
        }).show();
	}

	//================================= IMPRIMIR ORDEN DE COMPRA ====================================//
	function imprimirOrdenCompra(){ window.open("ordenes_compra/imprimir_orden_compra.php?id="+'<?php echo $id_orden_compra; ?>&opcGrillaContable=OrdenCompra'); }

	//================================ ELIMINA ORDENES DE COMPRA ====================================//
	function btnDeleteOrdenCompra(){
		if ('<?php echo $estado; ?>'=='3') { alert("Aviso\nEl documento ya ha esta cancelado"); return;}
		if(confirm('Esta seguro de Eliminar la presente Orden de compra y su contenido relacionado')){
			// if ('<?php echo $estado; ?>' != 2){
   			//              cargando_documentos('Cancelando Documento...','');
   			//          }
            cargando_documentos('Cancelando Documento...','');
			Ext.get('render_btns_orden_compra').load({
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
		}
	}

	//================================ EXPORTAR ORDEN EN EXCEL ====================================//
	function imprimirOrdenCompraExcel(){  window.open("ordenes_compra/exportar_excel_orden_compra.php?id="+'<?php echo $id_orden_compra; ?>'); }

	function ventanaConfigurarInformeOC(){

		Win_Ventana_configurar_separador_ajuste_inventario = new Ext.Window({
		    width       : 500,
		    height      : 250,
		    id          : 'Win_Ventana_configurar_separador_orden_compra',
		    title       : 'Aplicar separador',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../compras/ordenes_compra/wizard_orden_compra_xls.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'cuerpoVentanaConfiguracionAjusteInventario',
		        }
		    }
		}).show();
	}

	function validarSelectOC(separadorId){
		const selectDecimales	  = document.getElementById('separadorDecimalesOC'),
			  selectMiles 		  = document.getElementById('separadorMilesOC')
        
		if(selectDecimales.value === selectMiles.value && separadorId === 'decimales'){
			selectMiles.value = (selectMiles.value === ',')? "." : ",";
		}
		else if(selectDecimales.value === selectMiles.value){
			selectDecimales.value = (selectDecimales.value === ',')? "." : ",";
		}
	}

	function validarArticulosOrdenCompra(){ return 2; }

	function restaurarOrdenCompra(){
		cargando_documentos('Restaurando Documento...','');
		Ext.get('render_btns_orden_compra').load({
			url		: 'ordenes_compra/bd/bd.php',
			scripts	: true,
			nocache	: true,
			params	:
			{
				opc           : 'restaurarOrdenCompra',
				idOrdenCompra : '<?php echo $id_orden_compra; ?>'
			}
		});
	}

	function modificarOrdenCompra(){
		if (confirm("Aviso!\nEsta seguro que quiere modificar el documento?")) {
			// if ('<?php echo $estado; ?>' != 2){
                // cargando_documentos('Editando Documento...','');
            // }
            cargando_documentos('Editando Documento...','');
            Ext.get('render_btns_orden_compra').load({
                url     : 'ordenes_compra/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opc           : 'modificarDocumentoGenerado',
					idOrdenCompra : '<?php echo $id_orden_compra; ?>',
					id_bodega     : document.getElementById("filtro_ubicacion_ordenes_compra").value
                }
            });
        }
	}

	function win_Select_moneda(){
		var orden_compra = document.getElementById('titleDocuementoOrdenCompra').innerHTML,
	 	id_orden_compra = orden_compra.replace(/[^\d]/g, '');

        windows_wizard_moneda = new Ext.Window({
			width       : 600,
			height      : 280,
			title       : "Seleccione una moneda",
			modal       : true,
			autoScroll  : false,
			autoDestroy : true,
			bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			items		:
			[
				{
					xtype		: 'panel',
					id			: 'contenedor_wizard_contrato',
					border		: false,
					bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
					autoLoad	:
					{
						// url		: 'cotizacion/wizard_cotizacion/wizard.php',
						url		: 'ordenes_compra/wizard_moneda.php',
						scripts	: true,
						nocache	: true,
						params	:
						{
							consecutivo : '<?php echo $id_orden_compra; ?>'
						}
					}
				}
			]
		}).show();
    }

     //==================================ESTA FUNCION CARGA LA VENTANA DE ENVIO DE EMAIL=======================//

    function ventanaEnviarCorreo_OrdenCompra(){

         var myalto  = Ext.getBody().getHeight();
         var myancho = Ext.getBody().getWidth();

         ventana_email = new Ext.Window({
             id          : 'Win_Ventana_EnviarOrden',
             title       : 'Enviar Cotizacion',
             iconCls     : 'pie2',
             width       : 950,
             height      : 530,
             modal       : true,
             autoDestroy : true,
             draggable   : false,
             resizable   : false,
             bodyStyle   : 'background-color:#DFE8F6;',
             autoLoad    :
             {
                 url     : "bd/mail_documentos_compras.php",
                 scripts : true,
                 nocache : true,
                 params  :
                 {
                     id  : '<?php echo $id_orden_compra; ?>',

                     opcGrillaContable : 'OrdenCompra',
                     urlImpresion      : 'ordenes_compra/imprimir_orden_compra.php'
                 }
             }

         }).show();
    }

    function ventanaAutorizarOrdenCompra(){

        Win_Ventana_autoriza_documento = new Ext.Window({
            width       : 650,
            height      : 400,
            id          : 'Win_Ventana_autoriza_documento',
            title       : 'Ventana de Autorizacion del Documento',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : 'ordenes_compra/bd/bd.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opc             : 'controladorAutorizaciones',
					id_orden_compra : '<?php echo $id_orden_compra; ?>',
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
                            handler     : function(){ BloqBtn(this); Win_Ventana_autoriza_documento.close(id) }
                        }
                    ]
                }
            ]
        }).show();

    }

    function autorizarOrdenCompra() {
    	var tipo_autorizacion = document.getElementById('tipo_autorizacion').value;
      if(tipo_autorizacion == ''){
				return;
			}
      Ext.get('divLoadAutorizacion').load({
        url     : 'ordenes_compra/bd/bd.php',
        scripts : true,
        nocache : true,
        params  : {
										opc               : 'autorizarOrdenCompra',
										id_orden_compra   : '<?php echo $id_orden_compra; ?>',
										tipo_autorizacion : tipo_autorizacion
          				}
      });
    }

    function autorizarOrdenCompraArea(id_row,id_area,orden) {
        var tipo_autorizacion = document.getElementById('tipo_autorizacion_'+id_row).value;
        // if (tipo_autorizacion=='') {return;}

        MyLoading2('on');

        Ext.get('loadAut').load({
            url     : 'ordenes_compra/bd/bd.php',
            scripts : true,
            nocache : true,
            params  :
            {
                opc                : 'autorizarOrdenCompraArea',
                id_documento       : '<?php echo $id_orden_compra; ?>',
                opcGrillaContable  : '',
                tipo_autorizacion  : tipo_autorizacion,
                id_area            : id_area,
                orden              : orden,
            }
        });
    }

</script>
