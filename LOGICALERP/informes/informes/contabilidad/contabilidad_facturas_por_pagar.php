<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		    INICIALIZACION DE LA CLASE  	  ///**/
	/**/											                      /**/
	/**/	       $informe = new MyInforme();				/**/
	/**/											                      /**/
	/**//////////////////////////////////////////////**/

	$id_empresa                     = $_SESSION['EMPRESA'];
	$id_sucursal_default            = $_SESSION['SUCURSAL'];
	$informe->InformeName			      =	'facturas_por_pagar'; //NOMBRE DEL INFORME
	$informe->InformeTitle			    =	'Facturas Por Pagar'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false';              //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		  =	'false';              //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin	=	'false';	            //FILTRO FECHA
	$informe->InformeExportarPDF	  =	"false";	            //SI EXPORTA A PDF
	$informe->InformeExportarXLS	  =	"false";	            //SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';
	$informe->InformeTamano         = "CARTA-HORIZONTAL";
	$informe->DefaultCls            = ''; 		              //RESET STYLE CSS
	$informe->HeightToolbar         = 80; 		              //HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;

	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_facturas_por_pagar');

	if($modulo == 'contabilidad'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				         INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															                              /**/
	/**/	   $informe->Link = $link;  	    //Conexion a la BD			/**/
	/**/	   $informe->inicializa($_POST);  //Variables POST			  /**/
	/**/	   $informe->GeneraInforme(); 	  //Inicializa la Grilla	/**/
	/**/															                              /**/
	/**//////////////////////////////////////////////////////////////**/
?>
<script>
	contCliente      = 1;
	arrayProveedores = new Array();

	//======================== CONFIGURACION DEL INFORME =======================//
	function ventanaConfigurarInforme(){
		var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

		Win_Ventana_configurar_facturas_pagar = new Ext.Window({
			width       : myancho - (myancho * 25 / 100),
			height      : myalto - (myalto * 20 / 100),
	    id          : 'Win_Ventana_configurar_facturas_pagar',
	    title       : 'Aplicar Filtros',
	    modal       : true,
	    autoScroll  : false,
	    closable    : true,
	    autoDestroy : true,
	    autoLoad    : {
							        url     : '../informes/informes/contabilidad/wizard_contabilidad_facturas_por_pagar.php',
							        scripts : true,
							        nocache : true,
							        params  : {
											            opc : 'cuerpoVentanaConfiguracionFacturasPorPagar',
												        }
							    	},
		  tbar        : [
				    					{
		                    xtype   : 'buttongroup',
		                    columns : 3,
		                    title   : 'Sucursal',
		                    items   : [
						                        {
					                            xtype       : 'panel',
					                            border      : false,
					                            width       : 160,
					                            height      : 45,
					                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
					                            autoLoad    : {
											                                url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
											                                scripts : true,
											                                nocache : true,
											                                params  : { opc  : 'facturas_por_pagar' }
												                            }
						                        }
                    							]
                			},
						          {
						            xtype       : 'button',
						            width       : 60,
						            height      : 56,
						            text        : 'Generar<br>Informe',
						            scale       : 'large',
						            iconCls     : 'genera_informe',
						            iconAlign   : 'top',
						            handler     : function(){ generarHtml() }
						          },
						          {
						            xtype       : 'button',
						            width       : 60,
						            height      : 56,
						            text        : 'Exportar<br>PDF',
						            scale       : 'large',
						            iconCls     : 'genera_pdf',
						            iconAlign   : 'top',
						            handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
						          },
						          {
						            xtype       : 'button',
						            width       : 60,
						            height      : 56,
						            text        : 'Exportar<br>Excel',
						            scale       : 'large',
						            iconCls     : 'excel32',
						            iconAlign   : 'top',
						            handler     : function(){ generarPDF_Excel('IMPRIME_XLS') }
						          },'-',
						          {
						            xtype       : 'button',
						            width       : 60,
						            height      : 56,
						            text        : 'Reiniciar<br>Filtros',
						            scale       : 'large',
						            iconCls     : 'restaurar',
						            iconAlign   : 'top',
						            handler     : function(){ resetFiltros() }
						          },'-',
						          {
					              xtype       : 'button',
					              width       : 60,
					              height      : 56,
					              text        : 'Regresar',
					              scale       : 'large',
					              iconCls     : 'regresar',
					              iconAlign   : 'top',
					              handler     : function(){ Win_Ventana_configurar_facturas_pagar.close() }
						          }
								    ]
		}).show();
	}

	//=========================== REINICIAR FILTROS ============================//
	function resetFiltros(){

		localStorage.plazo_por_vencer_facturas_pagar           = "";
		localStorage.vencido_1_30_facturas_pagar               = "";
		localStorage.vencido_31_60_facturas_pagar              = "";
		localStorage.vencido_61_90_facturas_pagar              = "";
		localStorage.vencido_mas_90_facturas_pagar             = "";
		localStorage.tipo_fecha_informe_facturas_pagar         = "";
		localStorage.MyInformeFiltroFechaFinal_facturas_pagar  = "";
		localStorage.MyInformeFiltroFechaInicio_facturas_pagar = "";
		localStorage.tipo_informe_facturas_por_pagar           = "";
		localStorage.sucursal_facturas_por_pagar               = "";
		arrayProveedores.length               = 0;

		Win_Ventana_configurar_facturas_pagar.close();
        ventanaConfigurarInforme();

	}

	//============================ GENERAR INFORME =============================//
	function generarHtml(){
		idClientes  = '';
		sqlCheckbox = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for(i = 0; i < arrayProveedores.length; i++){
			if(typeof(arrayProveedores[i]) != "undefined" && arrayProveedores[i] != ""){
				idClientes = (idClientes == '')? arrayProveedores[i] : idClientes + ',' + arrayProveedores[i];
			}
		}
		//Imprime observaciones
		imprimeObservaciones = document.getElementById('imprime_observaciones').checked;

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		plazo_por_vencer = document.getElementById('plazo_por_vencer');
		vencido_1_30	   = document.getElementById('vencido_1_30');
		vencido_31_60	   = document.getElementById('vencido_31_60');
		vencido_61_90	   = document.getElementById('vencido_61_90');
		vencido_mas_90	 = document.getElementById('vencido_mas_90');

		//SI TODO ESTA CHECKED, NO ENVIAMOS NINGUN PARAMETRO
		if(plazo_por_vencer.checked && vencido_1_30.checked	&&	vencido_31_60.checked	&& vencido_61_90.checked && vencido_mas_90.checked){
			localStorage.plazo_por_vencer_facturas_pagar = 'true';
			localStorage.vencido_1_30_facturas_pagar     = 'true';
			localStorage.vencido_31_60_facturas_pagar    = 'true';
			localStorage.vencido_61_90_facturas_pagar    = 'true';
			localStorage.vencido_mas_90_facturas_pagar   = 'true';
		}
		else{
			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if(plazo_por_vencer.checked){
				sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=0)' : '';
				localStorage.plazo_por_vencer_facturas_pagar = 'true';
			}
			else{
				localStorage.plazo_por_vencer_facturas_pagar = 'false';
			}

			if(vencido_1_30.checked){
				sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <= 30)';
				localStorage.vencido_1_30_facturas_pagar = 'true';
			}
			else{
				localStorage.vencido_1_30_facturas_pagar = 'false';
			}

			if(vencido_31_60.checked){
				sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <= 60)';
				localStorage.vencido_31_60_facturas_pagar = 'true';
			}
			else{
				localStorage.vencido_31_60_facturas_pagar = 'false';
			}

			if(vencido_61_90.checked){
				sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <= 90)';
				localStorage.vencido_61_90_facturas_pagar = 'true';
			}
			else{
				localStorage.vencido_61_90_facturas_pagar = 'false';
			}

			if(vencido_mas_90.checked){
				sqlCheckbox = (sqlCheckbox == '')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>90)';
				localStorage.vencido_mas_90_facturas_pagar = 'true';
			}
			else{
				localStorage.vencido_mas_90_facturas_pagar = 'false';
			}
		}

		var sucursal       = document.getElementById('filtro_sucursal_facturas_por_pagar').value;
		var elementos_tipo = document.getElementsByName('tipo_informe');
		var tipo_informe   = '';
		var ordenCampo     = document.getElementById('ordenCampo').value;
		var ordenFlujo     = document.getElementById('ordenFlujo').value;

		for(var i=0; i<elementos_tipo.length; i++) { if (elementos_tipo[i].checked) {tipo_informe=elementos_tipo[i].value;} }

		var elementos = document.getElementsByName("tipo_fecha_informe");

		for(var i=0; i<elementos.length; i++) { if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;} }

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }
		else if (tipo_fecha_informe=='rango_fechas') { MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value; }
		else{ return; }

		//GUARDAR VARIABLES PARA EL FILTRO POR FECHA DEL LOCALSTORAGE
		localStorage.tipo_fecha_informe_facturas_pagar         = tipo_fecha_informe;
		localStorage.MyInformeFiltroFechaFinal_facturas_pagar  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicio_facturas_pagar = MyInformeFiltroFechaInicio;
		localStorage.tipo_informe_facturas_por_pagar           = tipo_informe;
		localStorage.sucursal_facturas_por_pagar               = sucursal;
		localStorage.ordenCampo_facturas_por_pagar             = ordenCampo;
		localStorage.ordenFlujo_facturas_por_pagar             = ordenFlujo;

		//==================// CUENTAS DE PAGO //==================//
		var cuenta     = ""
		,	generalCheck = document.getElementById('check_todas_cuentas_pago_FC').checked
		,	camposCheck  = document.querySelectorAll('.check_cuentas_pago_FC');

		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  if(campo.checked == true){ cuenta += campo.value+','; };
			});
		}

		Ext.get('RecibidorInforme_facturas_por_pagar').load({
			url     : '../informes/informes/contabilidad/contabilidad_facturas_por_pagar_Result.php',
			text	  : 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :	{
									nombre_informe             : 'Cartera detallada de un Proveedor',
									idClientes                 : idClientes,
									tipo_fecha_informe         : tipo_fecha_informe,
									sqlCheckbox                : sqlCheckbox,
									MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
									MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
									tipo_informe               : tipo_informe,
									sucursal                   : sucursal,
									cuenta                     : cuenta,
									ordenCampo                 : ordenCampo,
									ordenFlujo                 : ordenFlujo,
									imprimeObservaciones	   : imprimeObservaciones
								}
		});

		document.getElementById("RecibidorInforme_facturas_por_pagar").style.padding = 20;
	}

	//==================== GENERAR ARCHIVO DESDE EL WIZARD =====================//
	function generarPDF_Excel(tipo_documento){

		idClientes  = '';
		sqlCheckbox = '';

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayProveedores.length; i++) {
			if (typeof(arrayProveedores[i])!="undefined" && arrayProveedores[i]!="") {
				idClientes=(idClientes=='')? arrayProveedores[i] : idClientes+','+arrayProveedores[i] ;
			}
		}

		//RECORREMOS LOS CHECKBOX PARA SABER CUALES FUERON SELECCIONADOS Y ENVIARLOS A LA CONSULTA
		plazo_por_vencer = document.getElementById('plazo_por_vencer');
		vencido_1_30     = document.getElementById('vencido_1_30');
		vencido_31_60    = document.getElementById('vencido_31_60');
		vencido_61_90    = document.getElementById('vencido_61_90');
		vencido_mas_90   = document.getElementById('vencido_mas_90');

		//SI TODO ESTA CHECKED, NO ENVIAMOS NINGUN PARAMETRO
		if (plazo_por_vencer.checked &&
			vencido_1_30.checked	 &&
			vencido_31_60.checked	 &&
			vencido_61_90.checked	 &&
			vencido_mas_90.checked	) {

			localStorage.plazo_por_vencer_facturas_pagar = 'true';
			localStorage.vencido_1_30_facturas_pagar     = 'true';
			localStorage.vencido_31_60_facturas_pagar    = 'true';
			localStorage.vencido_61_90_facturas_pagar    = 'true';
			localStorage.vencido_mas_90_facturas_pagar   = 'true';

		}else{

			//SINO SE HAN SELECIONADO UNOS Y OTROS NO, EN ESE CASO HACEMOS
			if (plazo_por_vencer.checked) { sqlCheckbox =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=0)' : '' ;}
			if (vencido_1_30.checked) { sqlCheckbox     =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>0 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<= 30)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>0 AND  DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <= 30)';}
			if (vencido_31_60.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=60)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>30 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <=60)' ; }
			if (vencido_61_90.checked) { sqlCheckbox    =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)<=90)' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>60 AND DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion) <=90)' ; }
			if (vencido_mas_90.checked) { sqlCheckbox   =(sqlCheckbox=='')? '(DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>90 )' : sqlCheckbox+' OR (DATEDIFF("<?php echo date("Y-m-d") ?>",CF.fecha_generacion)>90)' ; }
		}

		var sucursal       = document.getElementById('filtro_sucursal_facturas_por_pagar').value;
		var elementos_tipo = document.getElementsByName('tipo_informe');
		var tipo_informe   = '';
		var ordenCampo     = document.getElementById('ordenCampo').value;
		var ordenFlujo     = document.getElementById('ordenFlujo').value;

		for(var i=0; i<elementos_tipo.length; i++) {
			if (elementos_tipo[i].checked) {tipo_informe=elementos_tipo[i].value;}
		}

		var elementos = document.getElementsByName("tipo_fecha_informe");

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_fecha_informe=elementos[i].value;}
		}

		MyInformeFiltroFechaFinal=document.getElementById('MyInformeFiltroFechaFinal').value;

		if (tipo_fecha_informe=='corte') { MyInformeFiltroFechaInicio=''; }
		else if (tipo_fecha_informe=='rango_fechas') { MyInformeFiltroFechaInicio=document.getElementById('MyInformeFiltroFechaInicio').value; }
		else{ return; }

		//==================// CUENTAS DE PAGO //==================//
		//*********************************************************//
		var cuenta       = ""
		, 	generalCheck = document.getElementById('check_todas_cuentas_pago_FC').checked
		,	camposCheck  = document.querySelectorAll('.check_cuentas_pago_FC');

		if(generalCheck == false){
			[].forEach.call(camposCheck, function(campo) {
			  	if(campo.checked == true){ cuenta += campo.value+','; };
			});
		}

		var data   = tipo_documento+"=true"
					+"&nombre_informe=Facturas Por Pagar"
					+"&tipo_fecha_informe="+tipo_fecha_informe
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&idClientes="+idClientes
					+"&sqlCheckbox="+sqlCheckbox
					+"&sucursal="+sucursal
					+"&tipo_informe="+tipo_informe
					+"&cuenta="+cuenta
					+"&ordenCampo="+ordenCampo
					+"&ordenFlujo="+ordenFlujo;

		window.open("../informes/informes/contabilidad/contabilidad_facturas_por_pagar_Result.php?"+data);
	}

	//==================== VENTANA PARA BUSCAR LOS TERCEROS ====================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
    var myancho = Ext.getBody().getWidth();

    Win_VentanaCliente_<?php echo $opcGrillaContable; ?> = new Ext.Window({
			width       : myancho - (myancho * 30 / 100),
			height      : myalto - (myalto * 30 / 100),
      id          : 'Win_VentanaCliente_<?php echo $opcGrillaContable; ?>',
      title       : 'Proveedores',
      modal       : true,
      autoScroll  : false,
      closable    : false,
      autoDestroy : true,
      autoLoad    : {
					            url     : '../informes/BusquedaTerceros.php',
					            scripts : true,
					            nocache : true,
					            params  : {
																	tabla             : 'compras_facturas',
																	id_tercero        : 'id_proveedor',
																	tercero           : 'proveedor',
																	opcGrillaContable : 'facturas_por_pagar',
																	cargaFuncion      : '',
																	nombre_grilla     : ''
										            }
      							},
      tbar        : [
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

	//============================ MOSTRAR TERCEROS ============================//
	function checkGrilla(checkbox,cont){
		if(checkbox.checked == true){
			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
			var div = document.createElement('div');
      div.setAttribute('id','fila_cartera_cliente_'+cont);
      div.setAttribute('class','filaBoleta');
      document.getElementById('body_grilla_filtro').appendChild(div);

      //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			nit     = document.getElementById('nit_'+cont).innerHTML;
			tercero = document.getElementById('tercero_'+cont).innerHTML;

			var fila = `<div class="row" id="row_cliente_${cont}">
										 <div class="cell" data-col="1">${contCliente}</div>
										 <div class="cell" data-col="2">${nit}</div>
										 <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
										 <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCliente(${cont})" title="Eliminar Cliente"></div>
									</div>`;

      //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
      proveedoresConfigurados[cont] = fila;
      //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
      document.getElementById('fila_cartera_cliente_'+cont).innerHTML = proveedoresConfigurados[cont];
      contCliente++;

      //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
      arrayProveedores[cont] = checkbox.value;
		}
		else if(checkbox.checked == false){
			delete arrayProveedores[cont];
			delete proveedoresConfigurados[cont];
			(document.getElementById("fila_cartera_cliente_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_cliente_"+cont));
		}
	}

	//============================ ELIMINAR TERCEROS ===========================//
	function eliminaCliente(cont){
		delete arrayProveedores[cont];
		delete proveedoresConfigurados[cont];
		(document.getElementById("fila_cartera_cliente_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_cliente_"+cont));
	}

	//=========================== MOSTRAR CUENTAS PAGO =========================//
	function check_cuentas_pago_FC(estadoCheck){
		var estado      = (estadoCheck==true)? false: true
		,	camposCheck = document.querySelectorAll('.check_cuentas_pago_FC');

		if(estadoCheck == true){ document.getElementById('contenedor_check_cuentas_pago_FC').style.display = 'none'; }
		else{ document.getElementById('contenedor_check_cuentas_pago_FC').style.display = 'block'; }

		[].forEach.call(camposCheck, function(campo) {
		  	campo.checked = false;
		});
	}
</script>
