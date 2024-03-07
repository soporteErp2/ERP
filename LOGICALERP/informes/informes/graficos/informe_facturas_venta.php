<?php
	include('../../../../configuracion/conectar.php');
	include('../../../../configuracion/define_variables.php');

	error_reporting(1);

	include('../../../../misc/MyInforme/class.MyInforme.php');
	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$informe = new MyInforme();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

			$informe->InformeName			=	'facturas_venta';  	//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Grafico de Ventas';	//TITULO DEL INFORME

			// $informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			// $informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			// $informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			// $informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			// $informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA
			$informe->InformeFechaInicioFin	=	'false';	 //FILTRO FECHA
			//$informe->InformeExportarPDF	= 	"true";	 //SI EXPORTA A PDF
			$informe->BtnGenera             = 'false';

			$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_clientes');
			$informe->InformeExportarXLS    = 	"false"; //SI EXPORTA A XLS
			$informe->AreaInformeQuitaAncho = 0;
			$informe->AreaInformeQuitaAlto  = 170;
			$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR
			$informe->InformeTamano         = "CARTA-HORIZONTAL";

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

	function ventanaConfigurarInforme() {

		Win_Ventana_grafico_facturas_venta = new Ext.Window({
		    width       : 650,
			height      : 400,
		    id          : 'Win_Ventana_grafico_facturas_venta',
		    title       : 'Configuracion del informe de graficos',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'informes/graficos/wizard_informe_facturas_venta.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            var1 : 'var1',
		            var2 : 'var2',
		        }
		    },
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
                                params  : { opc  : 'facturadGraficos' }
                            }
                        }
                    ]
                },
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
		                    text        : 'Generar',
		                    scale       : 'large',
		                    iconCls     : 'genera_informe',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); generaFormato(); }
		                },
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : 'Regresar',
		                    scale       : 'large',
		                    iconCls     : 'regresar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); Win_Ventana_grafico_facturas_venta.close(id) }
		                }
		            ]
		        }
		    ]
		}).show();
	}
</script>