<?php

	include("../configuracion/conectar.php");
	$permiso_movimientos_contables = (user_permisos(81,'false') == 'true')? 'false' : 'true';
	$permiso_colgaap = (user_permisos(77,'false') == 'true')? 'false' : 'true';
	$permiso_niif    = (user_permisos(78,'false') == 'true')? 'false' : 'true';

 ?>
<script>
// console.log("<?php echo $permiso_colgaap ?>");
var PermisoConfigContabilidad = <?php echo user_permisos(13,'true') ?>;
//ARRAY PARA LOS TERCEROS DE LOS INFORMES
arrayClientes                   = new Array();
clientesConfigurados            = new Array();
checkBoxSelectAllCcosER = '';

arrayProveedores                = new Array();
proveedoresConfigurados         = new Array();

arrayterceros                   = new Array();
tercerosConfigurados            = new Array();

//VARIABLES BALANCE DE COMPROBACION
arraytercerosBC                 = new Array();
tercerosConfiguradosBC          = new Array();
checkBoxSelectAllTercerosBC     = '';

//VARIABLES LIBRO AUXILIAR
arraytercerosLA                 = new Array();
tercerosConfiguradosLA          = new Array();
checkBoxSelectAllTercerosLA     = '';
centroCostosConfiguradosLA  = new Array();
arrayCentroCostosLA         = new Array();

arrayConsecutivos               = new Array();
consecutivosConfigurados        = new Array();

//ARRAY PARA LOS CENTROS DE COSTOS SELECCIONADOS EN EL ESTADO DE RESULTADOS
arrayCentroCostos               = new Array();
arrayCodigosCentroCostos        = new Array();
centroCostosConfigurados        = new Array();
checkBoxSelectAll               = '';

//--------------VARIABLES INFORMES NIIF
//ESTADO DE RESULTADOS
arrayCentroCostosNiif           = new Array();
arrayCodigosCentroCostosNiif    = new Array();
centroCostosConfiguradosNiif    = new Array();
checkBoxSelectAllNiif           = '';

//VARIABLES BALANCE DE COMPROBACION
arraytercerosBCNiif             = new Array();
tercerosConfiguradosBCNiif      = new Array();
checkBoxSelectAllTercerosBCNiif =  '';

Ext.QuickTips.init();
Ext.onReady
(function()
	{
		new Ext.Viewport //TAB PRINCIPAL
		(
			{
			layout		: 'border',
			style 		: 'font-family:Tahoma, Geneva, sans-serif; font-size:12px;',
			items:
				[
					// {
					// 	region		: 'north',
					// 	xtype		: 'panel',
					// 	height		: 33,
					// 	border		: false,
					// 	margins		: '0 0 0 0',
					// 	html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
					// 	bodyStyle 	: 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
					// },
					{
						region			: 'center',
						xtype			: 'tabpanel',
						margins			: '0 0 0 0',
						deferredRender	: true,
						border			: false,
						activeTab		: 0,
						bodyStyle 		: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
						items			:
						[

					//PESTAÑA MOVIMIENTOS CONTABLES
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Movimientos Contables',
								iconCls 	: 'ventas16',
								disabled	: <?php echo $permiso_movimientos_contables; ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_movimientos_contables',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;height:100%;overflow-y:auto;',
										border		: false,
										autoLoad:
										{
												url		: 'panel_global_contabilidad.php',
												scripts	: true,
												nocache	: true
										}
									}

								]
							},
//====================================== INFORMES COLGAAP ==========================================//
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Informes Colgaap',
								iconCls 	: 'doc16',
								disabled	: <?php echo $permiso_colgaap ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_informes_colgaap',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
									}
								],
								tbar		:
								[
									// {
									// 	text		: 'Cartera',
									// 	width       : 60,
         //            					height      : 56,
									// 	scale		: 'small',
									// 	iconCls		: 'doc',
									// 	iconAlign	: 'top',
									// 	handler		: function(){ informe('contabilidad_cartera_edades.php'); }
									// },
									{
										text		: 'Facturas <br/>por pagar',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_facturas_por_pagar.php'); }
									},
									// {
									// 	text		: 'Balance <br/>de prueba',
									// 	width       : 60,
         //            					height      : 56,
									// 	scale		: 'small',
									// 	iconCls		: 'doc',
									// 	iconAlign	: 'top',
									// 	handler		: function(){ informe('contabilidad_balance_prueba.php'); }
									// },
									{
										text		: 'Balance de<br/>Comprobacion',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_balance_comprobacion.php'); }
									},
									{
										text		: 'Estado de <br/>Resultado',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_estado_de_resultado.php'); }
									},
									{
										text		: 'Balance <br/>General',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_balance_general.php'); }
									},
									{
										text		: 'Libro <br/>Mayor',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_libro_mayor.php'); }
									},
									{
										text		: 'Libro <br/>Auxiliar',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('contabilidad_libro_auxiliar.php'); }
									},
									{
										text		: 'Punto de Venta<br/>POS',
										tooltip       : 'Genere Informes de ventas de uno o mas POS',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe('pos.php','pos'); }
									}
								]
							},
//====================================== INFORMES NIIF ==========================================//
							{
								closable	: false,
								autoScroll	: false,
								title		: 'Informes Niif',
								iconCls 	: 'doc16',
								disabled	: <?php echo $permiso_niif ?>,
								bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
								items		:
								[
									{
										xtype		: "panel",
										id			: 'contenedor_informes_niif',
										bodyStyle 	: 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
										border		: false,
									}
								],
								tbar		:
								[
									{
										text		: 'Estado Situacion<br>Financiera',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe_niif('estado_situacion_financiera.php'); }
									},
									{
										text		: 'Estado de<br>Resultado',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe_niif('contabilidad_estado_de_resultado.php'); }
									},
									{
										text		: 'Estado de<br>Flujo de Efectivo',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe_niif('contabilidad_estado_flujo_efectivo.php'); }
									},
									{
										text		: 'Balance de<br>Comprobacion',
										width       : 60,
                    					height      : 56,
										scale		: 'small',
										iconCls		: 'doc',
										iconAlign	: 'top',
										handler		: function(){ informe_niif('contabilidad_balance_comprobacion.php'); }
									}
								]
							}
						]
					}
				]
			}
		);
	}
);

	function informe(cual,opc){
		if (opc=='pos') {
			url='../informes/informes/pos/'+cual;
			modulo = 'pos';
		}
		else{
			url='../informes/informes/contabilidad/'+cual;
			modulo = 'contabilidad';
		}
		Ext.getCmp('contenedor_informes_colgaap').load({
			url 	: url,
			scripts	: true,
			nocache	: true,
			params	: { modulo : modulo }
		});
	}

	function informe_niif(cual,opc){

		url    = '../informes/informes/contabilidad_niif/'+cual;

		Ext.getCmp('contenedor_informes_niif').load({
			url 	: url,
			scripts	: true,
			nocache	: true,
			params	: { modulo : 'contabilidad' }
		});
	}

	function cargarContenedorDevolucionCompra(){
		Ext.getCmp('contenedor_movimientos_contables').load({
			url 	: 'notas_inventario/notas_devolucion/subindex.php',
			scripts	: true,
			nocache	: true,
			params	: { modulo:'contabilidad' }
		});
	}

	function cargarContenedorDevolucionVenta(){
		Ext.getCmp('contenedor_movimientos_contables').load({
			url 	: '../informes/informes/contabilidad/'+cual,
			scripts	: true,
			nocache	: true,
			params	: { modulo:'contabilidad' }
		});
	}

	function saldoCuentaDetalle(nameGrilla){
		var cont          = 0
		,	date          = "<?php echo date('Y-m-d'); ?>"
		,	simboloMoneda = "<?php echo $_SESSION['SIMBOLOMONEDA']; ?>"
		,	style         = ""
		,	id_fila_item  = ""
		,	debito        = 0
		,	credito       = 0
		,	saldoDebito   = 0
		,	saldoCredito  = 0
		,	saldoCuenta   = 0;

		var arrayFilas=document.getElementById('DIV_listado_'+nameGrilla).childNodes;		//Se atrapa todo los elementos hijos de la grilla

		for(filas in arrayFilas){
			if(arrayFilas[filas].className=="my_grilla_celdas2"){
				id_fila_item=(arrayFilas[filas].id).replace('item_'+nameGrilla+'_',"");
				// .replace(/[^0-9]/g,'')
				debito  = document.getElementById('div_'+nameGrilla+'_debe_'+id_fila_item).innerHTML;
				debito  = debito.replace(/[^.0-9]/g,'');
				credito = document.getElementById('div_'+nameGrilla+'_haber_'+id_fila_item).innerHTML;
				credito = credito.replace(/[^.0-9]/g,'');

				if(debito > 0){
					saldoDebito += debito*1;
				}
				if(credito > 0){
					saldoCredito += credito*1;
				}
			}
		}
		saldoCuenta = saldoDebito - saldoCredito;

		document.getElementById('saldoConsultaCuenta_debito').innerHTML= '<div class="textoSaldo">Saldo Debito</div><div class="valorSaldo">'+simboloMoneda+'&nbsp;&nbsp;'+formato_numero(saldoDebito, 2, ',', '.')+'</div>';
		document.getElementById('saldoConsultaCuenta_credito').innerHTML= '<div class="textoSaldo">Saldo Credito</div><div class="valorSaldo valorSaldoBorder">'+simboloMoneda+'&nbsp;&nbsp;-'+formato_numero(saldoCredito, 2, ',', '.')+'</div>';
		document.getElementById('saldoConsultaCuenta').innerHTML= '<div class="textoSaldo">&nbsp;</div><div class="valorSaldo">'+simboloMoneda+' '+formato_numero(saldoCuenta, 2, ',', '.')+'</div>';
	}

	function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return 0; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles=new RegExp('(-?[0-9]+)([0-9]{3})');
            while(miles.test(numero)) { numero=numero.replace(miles, '$1' + separador_miles + '$2'); }
        }

        return numero;
    }

    function resizeHeadMyGrilla(divScroll, idHead){
    	if (!divScroll.querySelector(".body"+divScroll.id)) {
			return;
		}
		var idDivScroll  = divScroll.id
		,	widthBody    = (divScroll.querySelector(".body"+idDivScroll).offsetWidth)*1
		,	divHead      = document.getElementById(idHead)
		,	widthHead    = (divHead.offsetWidth)*1;

		if(isNaN(widthBody) || widthBody == 0 || widthBody == widthHead){ return; }
		else if(widthBody > widthHead){ divHead.setAttribute('style','width: calc(100% - 1px);'); }
		else if(widthBody < widthHead){ divHead.setAttribute('style','width: calc(100% - 18px);'); }
	}

	// FUNCION DE LA VENTANA MODAL
	function cargando_documentos(texto) {

		var contenido='<div id="experiment">'+
				            '<div id="cube">'+
				                    '<div class="face one">'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+
				                    '<div class="face two">'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                    '</div>'+
				                    '<div class="face three">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                    '</div>'+
				                    '<div class="face four">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el2"></div>  '+
				                    '</div>'+
				                    '<div class="face five">'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div>'+
				                        '<div id="cuadro" class="el3"></div> '+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+
				                    '<div class="face six">'+
				                        '<div id="cuadro" class="el2"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                        '<div id="cuadro" class="el1"></div>'+
				                    '</div>'+

				                    '<div class="face seven">'+
				                    '</div>'+
				            '</div>'+
				            '<div id="LabelCargando">'+texto+'</div>'+
				    '</div>';
		parentModal = document.createElement("div");
		parentModal.innerHTML = '<div id="modal">'+contenido+'</div>';
		parentModal.setAttribute("id", "divPadreModal");
		document.body.appendChild(parentModal);
		document.getElementById("divPadreModal").className = "fondo_modal";

		document.getElementById('experiment').style.top="calc(50% - 100px)";
		document.getElementById('experiment').style.left="calc(50% - 100px)";

	}

	// FUNCION PARA ELIMINAR TODAS LAS CUENTAS DEL CUERPO DE LA NOTA
    function eliminar_cuentas(opcGrillaContable,id_documento){
    	// SI NO EXISTE EL ELEMENTO ENTONCES RETURN
    	if (!document.getElementById('DivArticulos'+opcGrillaContable)) { return; }
    	// SI NO TIENE CONTENIDO ENTONCES RETURN
    	if (document.getElementById('DivArticulos'+opcGrillaContable).innerHTML=='') { return; }

        var elemento = document.getElementById("button-delete-acounts");
        var event_elemento = elemento.getAttribute('onclick');
        elemento.setAttribute('onclick','');
        var url = '';

        if (opcGrillaContable=='NotaGeneral') {carpeta='nota_general'}
        else if (opcGrillaContable=='NotaGeneralNiif') {carpeta='nota_general_niif'}
        else if (opcGrillaContable=='NotaCierre') {carpeta='nota_cierre'}

        if (confirm('Advertencia\nEsta a punto de eliminar todas las cuentas que forman parte del cuerpo del documento\nRealmente desea continuar?')) {
            cargando_documentos('Eliminando cuentas <br> del documento...');

            Ext.Ajax.request({
                url     : carpeta+'/bd/bd.php',
                params  :
                {
                    opcGrillaContable : opcGrillaContable,
                    id_documento      : id_documento,
                    opc               : 'eliminar_todas_cuentas'
                },
                success :function (result, request){
                            var respuesta = result.responseText.split('{.}')[0]
                            ,   error = result.responseText.split('{.}')[1];

                            console.log("Response: "+result.responseText+'\nRespuesta: '+respuesta+'\nError: '+error);

                            if(respuesta == 'true'){
                                elemento.setAttribute('onclick',event_elemento);
                                document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
                                // LIMPIAR EL CUERPO DE LA NOTA
                                if (document.getElementById('DivArticulos'+opcGrillaContable)) {
                                	document.getElementById('DivArticulos'+opcGrillaContable).innerHTML='';
                                	document.getElementById('debitoAcumulado'+opcGrillaContable).innerHTML = '0';
                                	document.getElementById('creditoAcumulado'+opcGrillaContable).innerHTML = '0';
                                	document.getElementById('totalAcumulado'+opcGrillaContable).innerHTML = '0';

                                	// SI ES DIFERENTE DE UNA NOTA DE CIERRE ENTONCES MOSTRAR UNA FILA PARA GREGAR DE NUEVO CUENTAS Y ACTUALIZAR EL CONTADOR
                                	if (opcGrillaContable!='NotaCierre') {
                                		document.getElementById('DivArticulos'+opcGrillaContable).innerHTML='<div class="bodyDivArticulos'+opcGrillaContable+'" id="bodyDivArticulos'+opcGrillaContable+'_1"></div>';
	                                	Ext.get('bodyDivArticulos'+opcGrillaContable+'_1').load({
	                                		url     : carpeta+'/bd/bd.php',
	                                		scripts : true,
	                                		nocache : true,
	                                		params  :
	                                		{
												opc               : 'cargaDivsInsertUnidadesConTercero',
												opcGrillaContable : opcGrillaContable,
												cont              : '1',
	                                		}
	                                	});
                                	}

                                }
                            }
                            else{
                                alert('Error\n'+error);
                                elemento.setAttribute('onclick',event_elemento);
                                document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
                            }

                        },
                failure : function(){
                    alert('Error de conexion!\nintentelo de nuevo');
                    elemento.setAttribute('onclick',event_elemento);
                    // document.getElementById("modal").parentNode.parentNode.removeChild(document.getElementById("modal").parentNode);
                }
            });

        }

    }

    function calcTotalExtrac(accion,valor_detalle,valor_extracto){

			if(accion == 'sumar'){
				subtotalExtractos        += valor_extracto;
				subtotalDetalleExtractos += valor_detalle;
			}
			else if(accion == 'restar'){
				subtotalExtractos        -= valor_extracto;
				subtotalDetalleExtractos -= valor_detalle;
			}

			document.getElementById('subtotalExtractos').innerHTML        = subtotalExtractos.toFixed(2);
			document.getElementById('subtotalDetalleExtractos').innerHTML = subtotalDetalleExtractos.toFixed(2);
			document.getElementById('totalAcumuladoExtractos').innerHTML  = (subtotalExtractos-subtotalDetalleExtractos).toFixed(2);

    }

    function nuevaAmortizacion(){
    	Ext.get('contenedor_Amortizacion').load({
    		url     : 'amortizaciones/amortizacion/grillaContable.php',
    		scripts : true,
    		nocache : true,
    		params  :
    		{
    			opcGrillaContable : 'Amortizacion',
    		}
    	});
    }

</script>
