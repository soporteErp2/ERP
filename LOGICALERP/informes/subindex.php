<?php
	include('config_head.php');

	$contIndices = count($arrayHead);
	$tbarHtml    = "";
	for ($i=1; $i <= $contIndices ; $i++) {
			$tbarHtml .= "{
							text     : '".$arrayHead[$i]['text']."',
							iconCls  : '".$arrayHead[$i]['iconCls']."',
							scale    : '".$arrayHead[$i]['scale']."',
							disabled : '".$arrayHead[$i]['disabled']."',
							menu     :
							[";

		$contInformes = count($arrayHead[$i]['items']);
		for ($j=1; $j <= $contInformes ; $j++) {
				if($arrayHead[$i]['items'][$j]['activo'] != 'true')continue;
				$tbarHtml .= "	{
									text    : '<b>".$arrayHead[$i]['items'][$j]['text']."</b>',
									iconCls : '".$arrayHead[$i]['items'][$j]['iconCls']."',
									handler : function()
									{
										Ext.get(\"recibidor_informes\").load
										(
											{
												url		: '".$arrayHead[$i]['items'][$j]['url']."',
												scripts	: true,
												nocache : true,
												params  :
												        {
															id_formato : '".$arrayHead[$i]['items'][$j]['id']."',
												        }
											}
										)
									}
								},";
		}

			$tbarHtml .= "	]
						},";
	}
?>
<script>
	var PermisoConfigContabilidad = <?php echo user_permisos(13,'true') ?>;

	//ARRAY PARA LOS TERCEROS DE LOS INFORMES
	arrayClientes                = new Array();
	clientesConfigurados         = new Array();
	cuentasPagoVentaConfigurados = new Array();
	arraycuentasPagoVenta        = new Array();

	arrayProveedores        = new Array();
	proveedoresConfigurados = new Array();

	arrayterceros        = new Array();
	tercerosConfigurados = new Array();

	arrayConsecutivos        = new Array();
	consecutivosConfigurados = new Array();

	//VARIABLES BALANCE DE COMPROBACION
	arraytercerosBC                 = new Array();
	tercerosConfiguradosBC          = new Array();
	checkBoxSelectAllTercerosBC     = '';

	//VARIABLES LIBRO AUXILIAR
	arraytercerosLA             = new Array();
	tercerosConfiguradosLA      = new Array();
	checkBoxSelectAllTercerosLA = '';
	centroCostosConfiguradosLA  = new Array();
	arrayCentroCostosLA         = new Array();

	//ARRAY PARA LOS CENTROS DE COSTOS SELECCIONADOS EN EL ESTADO DE RESULTADOS
	arrayCentroCostos        = new Array();
	arrayCodigosCentroCostos = new Array();
	centroCostosConfigurados = new Array();
	checkBoxSelectAllCcosER  = '';

	//ARRAY PARA LOS CENTROS DE COSTOS SELECCIONADOS EN EL ESTADO DE RESULTADOS POR CENTROS DE COSTOS
	arrayCentroCostosERC         = new Array();
	arrayCodigosCentroCostosERC  = new Array();
	centroCostosConfiguradosERC  = new Array();
	checkBoxSelectAllERC         = '';
	checkBoxSelectAllTercerosERC = '';
	arraytercerosERC             = new Array();
	tercerosConfiguradosERC      = new Array();

	// ARRAY PARA LOS FILTROS DE ERP-REPORT
	arraytercerosERPR            = new Array();
	tercerosConfiguradosERPR     = new Array();
	arrayCentroCostosERPR        = new Array();
	arrayCodigosCentroCostosERPR = new Array();
	centroCostosConfiguradosERPR = new Array();

	//============================// INFORME NIIF //============================//
	//**************************************************************************//

	//ESTADO DE RESULTADOS
	arrayCentroCostosNiif           = new Array();
	arrayCodigosCentroCostosNiif    = new Array();
	centroCostosConfiguradosNiif    = new Array();
	checkBoxSelectAllNiif           = '';

	//VARIABLES BALANCE DE COMPROBACION
	arraytercerosBCNiif             = new Array();
	tercerosConfiguradosBCNiif      = new Array();
	checkBoxSelectAllTercerosBCNiif = '';

	//VARIABLES LIBRO AUXILIAR NIIF
	arraytercerosLANIIF             = new Array();
	tercerosConfiguradosLANIIF      = new Array();
	checkBoxSelectAllTercerosLANIIF = '';
	centroCostosConfiguradosLANIIF  = new Array();
	arrayCentroCostosLANIIF         = new Array();

	//===========================// INFORME VENTAS //===========================//
	//**************************************************************************//

	//COTIZACIONES
	arraytercerosCV          = new Array();
	tercerosConfiguradosCV   = new Array();
	arrayvendedoresCV        = new Array();
	vendedoresConfiguradosCV = new Array();

	//PEDIDOS
	arraytercerosPV          = new Array();
	tercerosConfiguradosPV   = new Array();
	arrayvendedoresPV        = new Array();
	vendedoresConfiguradosPV = new Array();

	//REMISIONES VENTA
	arraytercerosRV          		= new Array();
	tercerosConfiguradosRV   		= new Array();
	arrayvendedoresRV        		= new Array();
	vendedoresConfiguradosRV 		= new Array();
	checkBoxMostrarArticulos 		= '';
	arrayCentroCostosRV        	= new Array();
	CentroCostosConfiguradosRV 	= new Array();

	//ITEMS VENTA
	array_terceros_items    = new Array();
	terceros_config_items   = new Array();
	array_vendedores_items  = new Array();
	vendedores_config_items = new Array();
	array_ccos_items        = new Array();
	ccos_config_Items       = new Array();
	array_categorias_items  = new Array();
	categorias_config_Items = new Array();

	//ITEMS VENTA REMISIONADOS
	array_terceros_itemsRemisionados    = new Array();
	terceros_config_itemsRemisionados   = new Array();
	array_vendedores_itemsRemisionados  = new Array();
	vendedores_config_itemsRemisionados = new Array();
	array_ccos_itemsRemisionados        = new Array();
	ccos_config_ItemsRemisionados       = new Array();
	array_categorias_itemsRemisionados  = new Array();
	categorias_config_ItemsRemisionados = new Array();

	//FACTURAS
	arraytercerosFV            = new Array();
	tercerosConfiguradosFV     = new Array();
	arrayvendedoresFV          = new Array();
	vendedoresConfiguradosFV   = new Array();
	arrayCentroCostosFV        = new Array();
	CentroCostosConfiguradosFV = new Array();

	//FACTURAS POR PERIODOS
	arraytercerosFP            = new Array();
	tercerosConfiguradosFP     = new Array();
	arrayCentroCostosFP        = new Array();
	centroCostosConfiguradosFP = new Array();

	//FACTURAS ARCHIVOS ADJUNTOS
	arraytercerosFAA        = new Array();
	tercerosConfiguradosFAA = new Array();

	//RECIBO DE CAJA ARCHIVOS ADJUNTOS
	arraytercerosRCAA        = new Array();
	tercerosConfiguradosRCAA = new Array();

	//IMPUESTOS Y RETENCIONES EN VENTA
	arrayTerceros_FVIR          = new Array();
	tercerosConfigurados_FVIR   = new Array();
	arrayVendedores_FVIR        = new Array();
	vendedoresConfigurados_FVIR = new Array();
	conceptosConfigurados_FVIR  = new Array();
	arrayCentroCostos_FVIR      = new Array();

	//RECIBOS DE CAJA
	arraytercerosRC          = new Array();
	tercerosConfiguradosRC   = new Array();
	arrayvendedoresRC        = new Array();
	vendedoresConfiguradosRC = new Array();

	//FACTURAS RADICADAS
	arrayCentroCostosFR				 = new Array();
	centroCostosConfiguradosFR = new Array();

	//==========================// INFORME COMPRAS //===========================//
	//**************************************************************************//


	//COMPROBANTE DE EGRESO
	arraytercerosCE        	   = new Array();
	tercerosConfiguradosCE 	   = new Array();

	//REQUISICION
	solicitanteConfigurado     = new Array();
	arraySolicitante           = new Array();
	centroCostosConfiguradosRQ = new Array();
	arrayCentroCostosRQ        = new Array();

	//ORDENES DE COMPRA
	arraytercerosOC            = new Array();
	tercerosConfiguradosOC     = new Array();
	arrayempleadosOC        	 = new Array();
	empleadosConfiguradosOC    = new Array();
	arrayCentroCostosOC        = new Array();

	//FACTURAS DE COMPRA
	arraytercerosFC            = new Array();
	tercerosConfiguradosFC     = new Array();
	arrayvendedoresFC          = new Array();
	vendedoresConfiguradosFC   = new Array();
	arrayCentroCostosFC        = new Array();
	CentroCostosConfiguradosFC = new Array();

	// IMPUESTOS Y RETENCIONES EN COMPRA
	arrayTerceros_FCIR         = new Array();
	tercerosConfigurados_FCIR  = new Array();
	conceptosConfigurados_FCIR = new Array();

	//IMPUESTOS Y RETENCIONES
	arrayCentroCostos_FCIR     = new Array();

	//=========================// INFORME DE NOMINA //==========================//
	//**************************************************************************//

	arrayConceptosPlanillaAjusteNomina = new Array();
	arrayEmpleadosPlanillaAjuste       = new Array();
	arrayEmpleadosPlanillaAjusteNomina = new Array();




	//DEVOLUCIONES DE VENTA
	arraytercerosNDV          = new Array();
	tercerosConfiguradosNDV   = new Array();
	arrayvendedoresNDV        = new Array();
	vendedoresConfiguradosNDV = new Array();

	//DEVOLUCIONES DE VENTA
	arraytercerosNDC          = new Array();
	tercerosConfiguradosNDC   = new Array();
	arrayvendedoresNDC        = new Array();
	vendedoresConfiguradosNDC = new Array();

	//VARIABLES FACTURAS DE COMPRA
	arraytercerosAA             = new Array();
	tercerosConfiguradosAA      = new Array();
	checkBoxSelectAllTercerosAA = '';
	centroCostosConfiguradosAA  = new Array();

	//VARIABLES ENTRADA ALMACEN
	arraytercerosEA             = new Array();
	tercerosConfiguradosEA      = new Array();
	checkBoxSelectAllTercerosEA = '';
	centroCostosConfiguradosEA  = new Array();

	//VARIABLES ORDENES DE COMPRA
	arraytercerosOC             = new Array();
	tercerosConfiguradosOC      = new Array();
	checkBoxSelectAllTercerosOC = '';
	centroCostosConfiguradosOC  = new Array();

	//========================// INFORME CONTABILIDAD //========================//
	//**************************************************************************//

	//IMPUESTOS Y RETENCIONES
	arrayTerceros_CIR          = new Array();
	tercerosConfigurados_CIR   = new Array();
	conceptosConfigurados_CIR  = new Array();

	//INFORME TERCEROS (CRM)
	array_funcionarios_Terceros  = new Array();
	funcionarios_config_Terceros = new Array();

	//INFORME CONTACTOS POR TERCERO
	arrayproveedoresTC        = new Array();
	proveedoresConfiguradosTC = new Array();
	checkboxConContactos      = "";
	checkboxSinContactos      = "";

	//==============================// GRAFICAS //==============================//
	//**************************************************************************//

	// FACTURAS DE VENTA
	arraytercerosFVG        = new Array();
	tercerosConfiguradosFVG = new Array();

	// var Tam     = parent.TamVentana();
	// var myancho = Tam[0];
	// var myalto  = Tam[1];
	apuntador_este_gridraro = 2;

	var panelinfos = new Ext.Panel({
		frame     : false,
		border    : false,
		layout    : 'fit',
		//id      : 'recibidor_informes',
		html      : '<div id="recibidor_informes"></div>',
		bodyStyle : 'background-color: #DFE8F6; margin:0px; padding:0px',

	});

	var mytabgroup = new Ext.Panel(
	{
		region    : 'center',
		frame     : false,
		border    : false,
		margin    : '0 0 0 0',
		layout    : 'fit',
		bodyStyle : 'background-color: #DFE8F6; margin:0px; padding:0px',
		tbar      : [ <?php echo $tbarHtml; ?> ],
		items     : [ panelinfos ]
    });

    Ext.QuickTips.init();

	Ext.onReady(function(){
		var viewport = new Ext.Viewport({
			layout:'border',
			items:
			[
				// {
                // 	region      : 'north',
                //     xtype       : 'panel',
                //     height      : 33,
                //     border      : false,
                //     margins     : '0 0 0 0',
				// 	html		: '<div class="DivNorth" style="float:left;"><?php echo $_SESSION["NOMBREEMPRESA"] ." - ". $_SESSION["NOMBRESUCURSAL"]?></div><div class="DivNorth" style="float:right; text-align:right;"><?php echo $_SESSION["NOMBREFUNCIONARIO"] ?></div>',
                //     bodyStyle   : 'background-image:url(../../temas/clasico/images/fondo_cabecera.png);'
				// },
				mytabgroup
			]
		});
    });


	Ext.apply(Ext.form.VTypes, {
		daterange : function(val, field) {
			var date = field.parseDate(val);

			if(!date){ return; }
			if (field.startDateField && (!this.dateRangeMax || (date.getTime() != this.dateRangeMax.getTime()))) {
				var start = Ext.getCmp(field.startDateField);
				start.setMaxValue(date);
				start.validate();
				this.dateRangeMax = date;
			}
			else if (field.endDateField && (!this.dateRangeMin || (date.getTime() != this.dateRangeMin.getTime()))) {
				var end = Ext.getCmp(field.endDateField);
				end.setMinValue(date);
				end.validate();
				this.dateRangeMin = date;
			}
			return true;
		}
	});


	function formato_numero(numero, decimales, separador_decimal, separador_miles){ // v2007-08-06

        numero=parseFloat(numero);
        if(isNaN(numero)){ return 0; }
        if(decimales!==undefined){ numero=numero.toFixed(decimales); }  // Redondeamos

        // Convertimos el punto en separador_decimal
        numero=numero.toString().replace('.', separador_decimal!==undefined ? separador_decimal : ',');

        if(separador_miles){
            // Añadimos los separadores de miles
            var miles= new RegExp('(-?[0-9]+)([0-9]{3})');
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

	function agregaOptionSelectOrder(select,name,value){
		select.innerHTML += '<option value="'+value+'">'+name+'</option>';
	}

	function quitaOptionSelectOrder(select,value){
		var tmsel = select.length;
		for(i = 0;i < tmsel;i++){
			t = select.options[i].value;
			if(t == value){
				select.remove(i);
				localStorage.filtroOrden_itemsRemisionados = '';
			}
			tmsel = select.length;
		}
	}

	function manageDivsConfig(div){
		//MENU DESPLEGABLE DE LOS FILTROS EN LA VENTANA DE CONFIGURACION INFORMES
		var img     = document.getElementById('img_'+div);
		var display = document.getElementById(div).style.display;
		//alert(display);
		if(display == "none"){
			if(div == "divFechas"){
				document.getElementById(div).style.display="table";
			}
			else{
				document.getElementById(div).style.display="block";
			}
			//CODIGO PARA ROTAR IMAGENES
			img.style.transform        = "rotate(0deg)";
        	img.style.transition       = 'all 1s';
        	img.style.WebkitTransition = 'all 1s';
        	img.style.MozTransition    = 'all 1s';
		}
		else{
			document.getElementById(div).style.display="none";
			var img = document.getElementById('img_'+div);
        	img.style.transform 	   = "rotate(90deg)";
        	img.style.transition       = 'all 1s';
        	img.style.WebkitTransition = 'all 1s';
        	img.style.MozTransition    = 'all 1s';
		}
	}

	function validarCampoNumerico(event,input){

          numero = input.value;
          patron = /[^\d.]/g;
          if(patron.test(numero)){
              numero      = numero.replace(patron,'');
              input.value = numero;
          }
      }

</script>
