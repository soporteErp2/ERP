<?php
	include('../../../../../configuracion/conectar.php');
	include('../../../../../configuracion/define_variables.php');
	include('../../../../../misc/MyInforme/class.MyInforme.php');

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa          = $_SESSION['EMPRESA'];
	$id_sucursal_default = $_SESSION['SUCURSAL'];

    // CONSULTAR LA INFORMACION DEL REPORTE A GENERAR
    $sql="SELECT codigo,nombre FROM informes_niif_formatos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_formato";
    $query=$mysql->query($sql,$mysql->link);
    $codigo = $mysql->result($query,0,'codigo');
    $nombre = $mysql->result($query,0,'nombre');

	$informe->InformeName           =	'erp_report';  //NOMBRE DEL INFORME
	$informe->InformeTitle          =	$nombre; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode  =	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu      =	'false'; //FILTRO EMPRESA, SUCURSAL
	$informe->InformeFechaInicioFin =	'false';	 //FILTRO FECHA

	// EDIT CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR

	$informe->InformeExportarPDF    = 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS    = 	"false";	//SI EXPORTA A XLS
	$informe->BtnGenera             = 'false';

	$informe->AreaInformeQuitaAncho = 	0;
	$informe->AreaInformeQuitaAlto  = 	170;

	$informe->InformeTamano 		= 	"CARTA-HORIZONTAL";


	$informe->AddBotton('Configurar','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_balance_prueba');


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


	function ventanaConfigurarInforme(){
		Win_Ventana_configurar_report = new Ext.Window({
		    width       : 750,
			height      : 550,
		    id          : 'Win_Ventana_configurar_report',
		    title       : 'Asistente de configuracion de informes',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad_niif/report_niif/wizard_report.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            id_formato : '<?php echo $id_formato; ?>',
		        }
		    }
		    ,
		    tbar        :
		    [

		        {
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : 'Filtro Sucursal',
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 160,
                            height      : 45,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../funciones_globales/filtros/filtro_unico_sucursal_contabilidad_documentos.php',
                                scripts : true,
                                nocache : true,
                                params  : { opc  : 'sucursales_report' }
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
                    handler     : function(){ generarHtml(); BloqBtn(this); }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>PDF',
                    scale       : 'large',
                    iconCls     : 'genera_pdf',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_PDF'); BloqBtn(this); }
                },
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Exportar<br>Excel',
                    scale       : 'large',
                    iconCls     : 'excel32',
                    iconAlign   : 'top',
                    handler     : function(){ generarPDF_Excel('IMPRIME_XLS'); BloqBtn(this); }
                },'-',
                 // {
                    // xtype       : 'button',
                    // width       : 60,
                    // height      : 56,
                    // text        : 'Reiniciar<br>Filtros',
                    // scale       : 'large',
                    // iconCls     : 'restaurar',
                    // iconAlign   : 'top',
                    // handler     : function(){ resetFiltros() }
                // },'-',
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_configurar_report.close() }
                }
		    ]
		}).show();
	}

	function generarHtml(){
        // VALIDAR QUE SELECCIONO UN FORMATO
        // var id_formato = document.getElementById('id_formato').value;
        // if (id_formato=='' || id_formato==0){ alert("Aviso\nDebe seleccionar primero un informe"); return; }

        var separador_miles     = document.getElementById('separador_miles').value
        ,   separador_decimales = document.getElementById('separador_decimales').value

        // CAPTURAR VARIABLES
        // var cuentaInicial              = document.getElementById('cuenta_inicial').value
        // ,   cuentaFinal                = document.getElementById('cuenta_final').value
        var   sucursal                 = document.getElementById('filtro_sucursal_sucursales_report').value
        ,   MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio') ? document.getElementById('MyInformeFiltroFechaInicio').value : ''
        ,   MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal') ? document.getElementById('MyInformeFiltroFechaFinal').value : ''
        // ,   MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
        // ,   arraytercerosJSON          = Array()
        // ,   arrayCentroCostosJSON      = Array()
        // ,   i = 0

        // if (cuentaInicial!="" && cuentaFinal=="" || cuentaInicial=="" && cuentaFinal!="") {
        //     alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
        //     return;
        // }

        // arraytercerosLA.map(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
        // arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        // arrayCentroCostosERPR.forEach(function(id_centro_costo) {  arrayCentroCostosJSON[i] = id_centro_costo; i++; });
        // arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

		Ext.get('RecibidorInforme_erp_report').load({
			url     : '../informes/informes/contabilidad_niif/report_niif/report_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
            timeout : 120000,
			params  :
			{
                id_formato                 : '<?php echo $id_formato; ?>',
                // cuentaInicial           : cuentaInicial,
                // cuentaFinal             : cuentaFinal,
                MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
                MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
                separador_miles            : separador_miles,
                separador_decimales        : separador_decimales,
                sucursal                   : sucursal,
                // arraytercerosJSON       : arraytercerosJSON,
                // arrayCentroCostosJSON      : arrayCentroCostosJSON,
			}
		});

        localStorage.separador_milesNiifR           = separador_miles;
        localStorage.separador_decimalesNiifR       = separador_decimales;
        localStorage.MyInformeFiltroFechaInicioNiif = MyInformeFiltroFechaInicio;
        localStorage.MyInformeFiltroFechaFinalNiifR = MyInformeFiltroFechaFinal;
		document.getElementById("RecibidorInforme_erp_report").style.padding = 20;

	}

	function generarPDF_Excel(tipo_documento){
        // VALIDAR QUE SELECCIONO UN FORMATO
        // var id_formato = document.getElementById('id_formato').value;
        // if (id_formato=='' || id_formato==0){ alert("Aviso\nDebe seleccionar primero un informe"); return; }
        // var separador_miles     = document.getElementById('separador_miles').value
        // ,   separador_decimales = document.getElementById('separador_decimales').value

        // CAPTURAR VARIABLES
        // var cuentaInicial              = document.getElementById('cuenta_inicial').value
        // ,   cuentaFinal                = document.getElementById('cuenta_final').value
        // var   sucursal                   = document.getElementById('filtro_sucursal_sucursales_report').value
        // ,   MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value
        // ,   MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value
        // ,   arraytercerosJSON          = Array()
        // ,   arrayCentroCostosJSON      = Array()
        // ,   i = 0

        // if (cuentaInicial!="" && cuentaFinal=="" || cuentaInicial=="" && cuentaFinal!="") {
        //     alert("Error!\nDigite las dos cuentas para la consulta por rango de cuentas");
        //     return;
        // }

        // arraytercerosLA.map(function(id_tercero) { arraytercerosJSON[i] = id_tercero; i++; });
        // arraytercerosJSON=JSON.stringify(arraytercerosJSON);

        // arrayCentroCostosERPR.forEach(function(id_centro_costo) {  arrayCentroCostosJSON[i] = id_centro_costo; i++; });
        // arrayCentroCostosJSON=JSON.stringify(arrayCentroCostosJSON);

        var separador_miles            = document.getElementById('separador_miles').value
        ,   separador_decimales        = document.getElementById('separador_decimales').value
        ,   sucursal                   = document.getElementById('filtro_sucursal_sucursales_report').value
        ,   MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio') ? document.getElementById('MyInformeFiltroFechaInicio').value : ''
        ,   MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal') ? document.getElementById('MyInformeFiltroFechaFinal').value : ''

        var data = tipo_documento+"=true"
                                    +"&id_formato=<?php echo $id_formato;?>"
                                    +"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio
                                    +"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal
                                    +"&separador_miles="+separador_miles
                                    +"&separador_decimales="+separador_decimales
                                    +"&sucursal="+sucursal
                                    // +"&cuentaInicial="+cuentaInicial
                                    // +"&cuentaFinal="+cuentaFinal
                                    // +"&arraytercerosJSON="+arraytercerosJSON
                                    // +"&arrayCentroCostosJSON="+arrayCentroCostosJSON

        window.open("../informes/informes/contabilidad_niif/report_niif/report_Result.php?"+data);

		// var bodyVar = 	'&id_formato='+id_formato+
						// '&fecha='+fecha


		// window.open("../informes/informes/tributario/contabilidad_medios_magneticos_Result.php?IMPRIME_XLS=true"+bodyVar);
	}

	// FUNCIONES DE FILTROS CONFIGURADOS POR EL USUARIO
	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTercero(){
		var myalto  = Ext.getBody().getHeight();
        var myancho = Ext.getBody().getWidth();

        Win_Ventana_buscar_tercero = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_Ventana_buscar_tercero',
            title       : 'Terceros',
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
					tabla                : 'terceros',
					id_tercero           : 'id',
					tercero              : 'nombre_comercial',
					opcGrillaContable 	 : 'report',
					cargaFuncion         : '',
					nombre_grilla        : '',
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
                    handler     : function(){ Win_Ventana_buscar_tercero.close(id) }
                }
            ]
        }).show();
	}

	function checkGrilla(checkbox,cont){

		if (checkbox.checked ==true) {

			var div   = document.createElement('div');
            div.setAttribute('id','row_tercero_'+cont);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro').appendChild(div);


            //CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            var nit     = document.getElementById('nit_'+cont).innerHTML
            ,   tercero = document.getElementById('tercero_'+cont).innerHTML;

            var fila = `<div class="row" id="row_tercero_${cont}">
                           <div class="cell" data-col="1">${contTercero}</div>
                           <div class="cell" data-col="2">${nit}</div>
                           <div class="cell" data-col="3" title="${tercero}">${tercero}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaTercero(${cont})" title="Eliminar Tercero"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            tercerosConfiguradosERPR[cont]=fila;
            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_tercero_'+cont).innerHTML=fila;
            contTercero++;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arraytercerosERPR[cont]=checkbox.value;

		}
		else if (checkbox.checked ==false) {
			delete arraytercerosERPR[cont];
			delete tercerosConfiguradosERPR[cont];
			(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaTercero(cont){

		delete arraytercerosERPR[cont];

		delete tercerosConfiguradosERPR[cont];
		(document.getElementById("row_tercero_"+cont)).parentNode.removeChild(document.getElementById("row_tercero_"+cont));
	}


	//=====================// VENTANA CENTROS DE COSTOS //=====================//
	//*************************************************************************//
	function ventanaBusquedaCcos() {

		Win_Ventana_buscar_centro_cotos = new Ext.Window({
		    width       : 400,
		    height      : 450,
		    id          : 'Win_Ventana_buscar_centro_cotos',
		    title       : 'Seleccione un Centro de costo',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/contabilidad/grilla_buscar_centro_costos.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opcGrillaContable : 'grillaCentroCostos',
					funcion           : 'renderizaResultadoVentanaCentroCosto(id,codigo,nombre)',
		        }
		    },
		    tbar        :
		    [
                {
                    xtype       : 'button',
                    width       : 60,
                    height      : 56,
                    text        : 'Regresar',
                    scale       : 'large',
                    iconCls     : 'regresar',
                    iconAlign   : 'top',
                    handler     : function(){ Win_Ventana_buscar_centro_cotos.close(id) }
                }
		    ]
		}).show();
	}

	//================== FUNCION PARA RENDERIZAR LOS RESULTADOS DE LA VENTANA DE CENTROS DE COSTOS =======================//
	function renderizaResultadoVentanaCentroCosto(id,codigo,nombre) {
		if (id!='' && codigo!='' && nombre!='') {
			//VALIDAR QUE LAS CUENTAS NO ESTEN YA AGREGADAS
			//CREAMOS LOS DIV DE LOS TERCEROS AÑADIDOS RECORRIENDO EL ARRAY DE LOS TERCEROS GUARDADOS
			var cadenaBuscar='';
			for ( i = 0; i < arrayCentroCostosERPR.length; i++) {
				if (typeof(arrayCentroCostosERPR[i])!="undefined" && arrayCentroCostosERPR[i]!="") {
					// console.log(codigo.indexOf(arrayCentroCostosERPR[i])+' - '+arrayCentroCostosERPR[i]+' - '+id);

					if (id.indexOf(arrayCentroCostosERPR[i])==0) {

					  alert("Ya se agrego el Centro de Costos, o el padre del centro de costos");
					  return;
					}
				}
			}

            var div   = document.createElement('div');
            div.setAttribute('id','row_centro_costo_'+id);
            div.setAttribute('class','row');
            document.getElementById('body_grilla_filtro_ccos').appendChild(div);

            var fila = `<div class="row" id="row_centro_costo_${id}">
                           <div class="cell" data-col="1"></div>
                           <div class="cell" data-col="2">${codigo}</div>
                           <div class="cell" data-col="3" title="${nombre}">${nombre}</div>
                           <div class="cell" data-col="1" data-icon="delete" onclick="eliminaCentroCostos(${id})" title="Eliminar Centro Costos"></div>
                        </div>`;

            //LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            centroCostosConfiguradosERPR[id]=fila;

            //CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            document.getElementById('row_centro_costo_'+id).innerHTML=fila;

            //LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            arrayCentroCostosERPR[id]=id;
		}
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCentroCostos(id){

		delete arrayCentroCostosERPR[id];
		delete centroCostosConfiguradosERPR[id];
		(document.getElementById("row_centro_costo_"+id)).parentNode.removeChild(document.getElementById("row_centro_costo_"+id));
	}

</script>