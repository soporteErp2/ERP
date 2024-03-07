<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');
	include('../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	$informe->InformeName			=	'itemsRemisionados';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Informe Items Remisionados'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	$informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"false";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";

	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 190;
	if($modulo=='ventas'){ $informe->AreaInformeQuitaAlto = 230; }

	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$informe->Link = $link;  	//Conexion a la BD			/**/
	/**/	$informe->inicializa($_POST);//variables POST			/**/
	/**/	$informe->GeneraInforme(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

?>

<script>
	contTercero     = 1;
	contVendedores  = 1;
	contCcos        = 1;
	contCategorias  = 1;
	nivelCategoria  = "familia";
	checkTotalItemsRemisionados    = "ninguno";
	checkOrderItemsRemisionados    = "ninguno";
	select_order_itemsRemisionados = "ninguno";

	allCategoriasItemsRemisionados = "false";
	allCcosItemsRemisionados       = "false";
	allVendedoresItemsRemisionados = "false";
	allClientesItemsRemisionados   = "false";

	function generarPDF_Excel_principal(tipo_documento){

		var sucursal = '<?php echo $id_sucursal; ?>';
		var MyInformeFiltroFechaFinal  = '';
		var MyInformeFiltroFechaInicio = '';

		if (typeof(localStorage.sucursal_itemsRemisionados)!='undefined') {
			if (localStorage.sucursal_itemsRemisionados!='') { sucursal=localStorage.sucursal_itemsRemisionados }
		}

		//CHECKS TOTALIZADOS
		// var checkTotal     = document.getElementsByName('totalizado_itemsRemisionados');
		// [].forEach.call(checkTotal,function(check,indice,documento){
		// 	if (check.checked) { totalizado = check.value; }
		// });

		var idTerceros   = '';
		var idVendedores = '';
		var idCcos       = '';
		var idCategorias = '';

		//ARRAY CLIENTES
		array_terceros_itemsRemisionados.forEach(function(valor,indice,documento){
			idTerceros = (idTerceros=='')? valor : idTerceros+','+valor;
		});
		if(allClientesItemsRemisionados == "true"){ idTerceros = "todos"; }

		//ARRAY VENDEDORES
		array_vendedores_itemsRemisionados.forEach(function(valor,indice,documento){
			idVendedores = (idVendedores=='')? valor : idVendedores+','+valor;
		});
		if(allVendedoresItemsRemisionados == "true"){ idVendedores = "todos"; }

		//ARRAY CENTROS DE COSTO
		array_ccos_itemsRemisionados.forEach(function(valor,indice,documento){
			idCcos = (idCcos=='')? valor : idCcos+','+valor;
		});
		if(allCcosItemsRemisionados == "true"){ idCcos = "todos"; }

		//ARRAY CATEGORIAS
		array_categorias_itemsRemisionados.forEach(function(valor,indice,documento){
			idCategorias = (idCategorias=='')? valor : idCategorias+','+valor;
		});
		if(allCategoriasItemsRemisionados == "true"){ idCategorias = "todos"; }

		var data = tipo_documento+"=true"
					+"&sucursal="+sucursal
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&idTerceros="+idTerceros
					+"&idVendedores="+idVendedores
					+"&idCcos="+idCcos
					+"&idCategorias="+idCategorias
					+"&nivelCategoria="+nivelCategoria
					+"&totalizado="+checkTotalItemsRemisionados
					+"&ordenaPor="+select_order_itemsRemisionados
					+"&ordenamiento="+checkOrderItemsRemisionados
					+"&limite="+limiteRows;

		window.open("../informes/informes/informes_ventas/items_remisionados_Result.php?"+data);
	}

	//=====================// VENTANA CONFIGURACION DE INFORME //=====================//
	//********************************************************************************//

	function ventanaConfigurarInforme(){

		var config = {
						width           : 170,
						todasSucursales : "true",
						todasBodegas    : "true",
						loadFuntion     : "",
					};

		Win_Ventana_configurar_itemsRemisionados = new Ext.Window({
		    width       : 670,
		    height      : 560,
		    id          : 'Win_Ventana_configurar_itemsRemisionados',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/informes_ventas/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            opc : 'ventana_configuracion_itemsRemisionados',
		        }
		    },
		    tbar        :
		    [

		        {
                    xtype   : 'buttongroup',
                    columns : 3,
                    // title   : 'Filtro Sucursal',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 240,
                            height      : 56,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_sucursal_bodega.php',
                                scripts : true,
                                nocache : true,
                                params  :
                                {
									opc    : 'itemsRemisionados',
									config : JSON.stringify(config),
                            	}
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
                    handler     : function(){ generarHtml_itemsRemisionados() }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_itemsRemisionados('IMPRIME_PDF') }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel_itemsRemisionados('IMPRIME_XLS') }
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
		            handler     : function(){ Win_Ventana_configurar_itemsRemisionados.close() }
		        }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalItemsRemisionados  = "";
		localStorage.MyInformeFiltroFechaInicioItemsRemisionados = "";
		localStorage.sucursal_itemsRemisionados                  = "";
		localStorage.filtroOrden_itemsRemisionados 				 = "";
		localStorage.limiteRows_itemsRemisionados  				 = "";
		array_terceros_itemsRemisionados.length                  = 0;
		array_vendedores_itemsRemisionados.length                = 0;
		array_ccos_itemsRemisionados.length                      = 0;
		array_categorias_itemsRemisionados.length                = 0;
		Win_Ventana_configurar_itemsRemisionados.close();
		ventanaConfigurarInforme();

		allClientesItemsRemisionados   = 'false';
		allVendedoresItemsRemisionados = 'false';
		allCcosItemsRemisionados       = 'false';
		allCategoriasItemsRemisionados = 'false';
	}

	function generarHtml_itemsRemisionados(){

		var MyInformeFiltroFechaFinal      = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio     = document.getElementById('MyInformeFiltroFechaInicio').value;
		var select_order_itemsRemisionados = document.getElementById('select_order_itemsRemisionados').value;

		var nivelCategoria = document.getElementById('nivelCategoriaItemsRemisionados').value;
		var sucursal       = document.getElementById('filtro_sucursal_itemsRemisionados').value;
		var checkTotal     = document.getElementsByName('totalizado_itemsRemisionados');
		var checkOrden     = document.getElementsByName('order_itemsRemisionados');
		var limiteRows     = document.getElementById('fieldLimiteRows_itemsRemisionados').value;
		var totalizado     = '';
		var ordenamiento   = '';

		//CHECKS TOTALIZADOS
		[].forEach.call(checkTotal,function(check,indice,documento){
			if (check.checked) { totalizado = check.value; }
		});

		//CHECKS ORDENA
		[].forEach.call(checkOrden,function(check,indice,documento){
			if (check.checked) { ordenamiento = check.value; }
		});

		var idTerceros   = '';
		var idVendedores = '';
		var idCcos       = '';
		var idCategorias = '';

		//ARRAY CLIENTES
		array_terceros_itemsRemisionados.forEach(function(valor,indice,documento){
			idTerceros = (idTerceros=='')? valor : idTerceros+','+valor;
		});
		if(allClientesItemsRemisionados == "true"){ idTerceros = "todos"; }

		//ARRAY VENDEDORES
		array_vendedores_itemsRemisionados.forEach(function(valor,indice,documento){
			idVendedores = (idVendedores=='')? valor : idVendedores+','+valor;
		});
		if(allVendedoresItemsRemisionados == "true"){ idVendedores = "todos"; }

		//ARRAY CENTROS DE COSTO
		array_ccos_itemsRemisionados.forEach(function(valor,indice,documento){
			idCcos = (idCcos=='')? valor : idCcos+','+valor;
		});
		if(allCcosItemsRemisionados == "true"){ idCcos = "todos"; }

		//ARRAY CATEGORIAS
		array_categorias_itemsRemisionados.forEach(function(valor,indice,documento){
			idCategorias = (idCategorias=='')? valor : idCategorias+','+valor;
		});
		if(allCategoriasItemsRemisionados == "true"){ idCategorias = "todos"; }

		Ext.get('RecibidorInforme_itemsRemisionados').load({
			url     : '../informes/informes/informes_ventas/items_remisionados_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'Items Remisionados de Venta',
				sucursal                   : sucursal,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
				idTerceros                 : idTerceros,
				idVendedores               : idVendedores,
				idCcos                     : idCcos,
				idCategorias               : idCategorias,
				nivelCategoria             : nivelCategoria,
				totalizado                 : totalizado,
				ordenaPor				   : select_order_itemsRemisionados,
				ordenamiento               : ordenamiento,
			    limite                     : limiteRows
			}
		});

		document.getElementById("RecibidorInforme_itemsRemisionados").style.padding = 20;

		localStorage.MyInformeFiltroFechaFinalItemsRemisionados  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioItemsRemisionados = MyInformeFiltroFechaInicio;
		localStorage.sucursal_itemsRemisionados    = sucursal;
		localStorage.filtroOrden_itemsRemisionados = select_order_itemsRemisionados;
		localStorage.limiteRows_itemsRemisionados  = limiteRows;
	}

	function generarPDF_Excel_itemsRemisionados(tipo_documento){

		var MyInformeFiltroFechaFinal  	   = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio 	   = document.getElementById('MyInformeFiltroFechaInicio').value;
		var select_order_itemsRemisionados = document.getElementById('select_order_itemsRemisionados').value;

		var nivelCategoria = document.getElementById('nivelCategoriaItemsRemisionados').value;
		var sucursal       = document.getElementById('filtro_sucursal_itemsRemisionados').value;
		var checkTotal     = document.getElementsByName('totalizado_itemsRemisionados');
		var checkOrden     = document.getElementsByName('order_itemsRemisionados');
		var limiteRows     = document.getElementById('fieldLimiteRows_itemsRemisionados').value;
		var totalizado     = '';
		var ordenamiento   = '';

		//ARRAY CLIENTES
		[].forEach.call(checkTotal,function(check,indice,documento){
			if (check.checked) { totalizado = check.value; }
		});

		//CHECKS ORDENA
		[].forEach.call(checkOrden,function(check,indice,documento){
			if (check.checked) { ordenamiento = check.value; }
		});

		var idTerceros   = '';
		var idVendedores = '';
		var idCcos       = '';
		var idCategorias = '';

		//ARRAY CLIENTES
		array_terceros_itemsRemisionados.forEach(function(valor,indice,documento){
			idTerceros = (idTerceros=='')? valor : idTerceros+','+valor;
		});
		if(allClientesItemsRemisionados == "true"){ idTerceros = "todos"; }

		//ARRAY VENDEDORES
		array_vendedores_itemsRemisionados.forEach(function(valor,indice,documento){
			idVendedores = (idVendedores=='')? valor : idVendedores+','+valor;
		});
		if(allVendedoresItemsRemisionados == "true"){ idVendedores = "todos"; }

		//ARRAY CENTROS DE COSTO
		array_ccos_itemsRemisionados.forEach(function(valor,indice,documento){
			idCcos = (idCcos=='')? valor : idCcos+','+valor;
		});
		if(allCcosItemsRemisionados == "true"){ idCcos = "todos"; }

		//ARRAY CATEGORIAS
		array_categorias_itemsRemisionados.forEach(function(valor,indice,documento){
			idCategorias = (idCategorias=='')? valor : idCategorias+','+valor;
		});
		if(allCategoriasItemsRemisionados == "true"){ idCategorias = "todos"; }


		var data = tipo_documento+"=true"
					+"&sucursal="+sucursal
					+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
					+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
					+"&idTerceros="+idTerceros
					+"&idVendedores="+idVendedores
					+"&idCcos="+idCcos
					+"&idCategorias="+idCategorias
					+"&nivelCategoria="+nivelCategoria
					+"&totalizado="+totalizado
					+"&ordenaPor="+select_order_itemsRemisionados
					+"&ordenamiento="+ordenamiento
					+"&limite="+limiteRows;

		window.open("../informes/informes/informes_ventas/items_remisionados_Result.php?"+data);
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero_itemsRemisionados(opc){
		if (opc=='vendedores') {
			tabla   = 'empleados';
			tercero = 'nombre';
			titulo_ventana = 'Empleados';
		}
		else{
			tabla   = 'terceros';
			tercero = 'nombre_comercial';
			titulo_ventana = 'Clientes';
		}

        Win_VentanaCliente_terceros_itemsRemisionados = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros_itemsRemisionados',
            title       : titulo_ventana,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/BusquedaTerceros.php',
                scripts : true,
                nocache : true,
                params  :
                {
					tabla             : tabla,
					id_tercero        : 'id',
					tercero           : tercero,
					opcGrillaContable : 'itemsRemisionados',
					cargaFuncion      : '',
					nombre_grilla     : '',
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
                    handler     : function(){ Win_VentanaCliente_terceros_itemsRemisionados.close(id) }
                }
            ]
        }).show();
	}

	//========================// FILTRO CENTROS DE COSTOS //========================//
	//******************************************************************************//
	function ventanaBusquedaCentroCostosItemsRemisionados(){
		Win_Ventana_buscar_centro_costos = new Ext.Window({
            width       : 450,
            height      : 410,
            id          : 'Win_Ventana_buscar_centro_costos',
            title       : 'Buscar Centro de Costos',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/grillaBuscarCentroCostos.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opcGrillaContable : 'itemsRemisionados',
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
                    handler     : function(){ Win_Ventana_buscar_centro_costos.close(id) }
                }
            ]
        }).show();
	}

	function checkGrillaCentroCostos(checkbox,cont){
		if (checkbox.checked == true) {
			var div = document.createElement('div');
        	div.setAttribute('id','fila_ccos_itemsRemisionados_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracionCentroCostos').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			var nit     = document.getElementById('codigo_'+cont).innerHTML;
			var tercero = document.getElementById('nombre_'+cont).innerHTML;

        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
			var fila = '<div class="campo0">'+contCcos+'</div><div class="campo1" style="width:120px" id="codigo_'+cont+'">'+nit+'</div><div class="campo2" style="width:180px;" id="nombre_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCentroCostosItemsRemisionados('+cont+')" title="Eliminar Cliente"></div>';
        	ccos_config_ItemsRemisionados[cont]=fila;

        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_ccos_itemsRemisionados_'+cont).innerHTML=fila;
        	contCcos++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	array_ccos_itemsRemisionados[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete array_ccos_itemsRemisionados[cont];
			delete ccos_config_ItemsRemisionados[cont];
			(document.getElementById("fila_ccos_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_ccos_itemsRemisionados_"+cont));
		}
	}

	function eliminaCentroCostosItemsRemisionados(cont,tabla){
		delete array_ccos_itemsRemisionados[cont];
		delete ccos_config_ItemsRemisionados[cont];
		(document.getElementById("fila_ccos_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_ccos_itemsRemisionados_"+cont));
	}

	//===========================// FILTRO CATEGORIAS //============================//
	//******************************************************************************//
	function ventanaBusquedaCategoriasItemsRemisionados(){
		var nivel = document.getElementById("nivelCategoriaItemsRemisionados").value;

		Win_Ventana_buscar_categorias = new Ext.Window({
            width       : 450,
            height      : 410,
            id          : 'Win_Ventana_buscar_categorias',
            title       : 'Buscar Categorias',
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : '../informes/grilla_categorias.php',
                scripts : true,
                nocache : true,
                params  :
                {
					opcGrilla : 'itemsRemisionados',
					nivel     : nivel
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
                    handler     : function(){ Win_Ventana_buscar_categorias.close(id) }
                }
            ]
        }).show();
	}

	function checkGrillaCategoriasItems(checkbox,cont){
		if (checkbox.checked == true) {
			var div = document.createElement('div');
        	div.setAttribute('id','fila_categorias_itemsRemisionados_'+cont);
        	div.setAttribute('class','filaBoleta');
        	document.getElementById('bodyTablaConfiguracionCategorias').appendChild(div);

        	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
			var nit     = document.getElementById('codigo_'+cont).innerHTML;
			var tercero = document.getElementById('nombre_'+cont).innerHTML;

        	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
			var fila = '<div class="campo0">'+contCategorias+'</div><div class="campo1" style="width:120px" id="codigo_'+cont+'">'+nit+'</div><div class="campo2" style="width:180px;" id="nombre_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCategoriasItemsRemisionados('+cont+')" title="Eliminar Categoria"></div>';
        	categorias_config_ItemsRemisionados[cont]=fila;

        	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
        	document.getElementById('fila_categorias_itemsRemisionados_'+cont).innerHTML=fila;
        	contCategorias++;

        	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
        	array_categorias_itemsRemisionados[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete array_categorias_itemsRemisionados[cont];
			delete categorias_config_ItemsRemisionados[cont];
			(document.getElementById("fila_categorias_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_categorias_itemsRemisionados_"+cont));
		}
	}

	function eliminaCategoriasItemsRemisionados(cont,tabla){
		delete array_categorias_itemsRemisionados[cont];
		delete categorias_config_ItemsRemisionados[cont];

		(document.getElementById("fila_categorias_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_categorias_itemsRemisionados_"+cont));
	}


	function checkGrilla(checkbox,cont,tabla){

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR
            if (tabla=='empleados') {
				var div = document.createElement('div');

            	div.setAttribute('id','fila_vendedor_itemsRemisionados_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracionVendedores').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
				var nit     = document.getElementById('nit_'+cont).innerHTML;
				var tercero = document.getElementById('tercero_'+cont).innerHTML;

            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
				var fila = '<div class="campo0">'+contVendedores+'</div><div class="campo1" id="nit_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="tercero_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteItemsRemisionados('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	vendedores_config_itemsRemisionados[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_vendedor_itemsRemisionados_'+cont).innerHTML=fila;
            	contVendedores++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	array_vendedores_itemsRemisionados[cont]=checkbox.value;
            }
            else{
				var div = document.createElement('div');
            	div.setAttribute('id','fila_tercero_itemsRemisionados_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracion').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
				var nit     = document.getElementById('nit_'+cont).innerHTML;
				var tercero = document.getElementById('tercero_'+cont).innerHTML;

            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
				var fila = '<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:150px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaClienteItemsRemisionados('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	terceros_config_itemsRemisionados[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_tercero_itemsRemisionados_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	array_terceros_itemsRemisionados[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {
			if (tabla=='empleados') {
				delete array_vendedores_itemsRemisionados[cont];
				delete vendedores_config_itemsRemisionados[cont];
				(document.getElementById("fila_vendedor_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_vendedor_itemsRemisionados_"+cont));
			}
			else{
				delete array_terceros_itemsRemisionados[cont];
				delete terceros_config_itemsRemisionados[cont];
				(document.getElementById("fila_tercero_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_tercero_itemsRemisionados_"+cont));
			}
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaClienteItemsRemisionados(cont,tabla){

		if (tabla=='empleados') {
			delete array_vendedores_itemsRemisionados[cont];
			delete vendedores_config_itemsRemisionados[cont];
			(document.getElementById("fila_vendedor_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_vendedor_itemsRemisionados_"+cont));
		}
		else{
			delete array_terceros_itemsRemisionados[cont];
			delete terceros_config_itemsRemisionados[cont];
			(document.getElementById("fila_tercero_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_tercero_itemsRemisionados_"+cont));
		}
	}

	function cambiarCategoriaItemsRemisionados(select){
		nivelCategoria = select.value;

		array_categorias_itemsRemisionados.forEach(function(cont,indice,argumento){
			delete array_categorias_itemsRemisionados[cont];
			delete categorias_config_ItemsRemisionados[cont];
			// console.log(cont);
			(document.getElementById("fila_categorias_itemsRemisionados_"+cont)).parentNode.removeChild(document.getElementById("fila_categorias_itemsRemisionados_"+cont));
		});

		// [].forEach.call(array_categorias_itemsRemisionados, function(campo) {
		//   	console.log(campo);
		// });

	}

</script>