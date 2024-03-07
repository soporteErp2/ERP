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
	$informe->InformeName			=	'Terceros';  //NOMBRE DEL INFORME
	$informe->InformeTitle			=	'Informes Terceros'; //TITULO DEL INFORME
	$informe->InformeEmpreSucuBode	=	'false'; //FILTRO EMPRESA, SUCURSAL, BODEGA
	$informe->InformeEmpreSucu		=	'false'; //FILTRO EMPRESA, SUCURSAL
	// $informe->InformeFechaInicio	=	'true';	 //FILTRO FECHA
	// $informe->AddFiltroFechaInicioFin('false','true');
	// $informe->AddBotton('Exportar PDF','genera_pdf','generarPDF_Excel_principal("IMPRIME_PDF")','Btn_exportar_pdf');
	$informe->AddBotton('Exportar Excel','excel32','generarPDF_Excel_principal("IMPRIME_XLS")','Btn_exportar_excel');
	$informe->AddBotton('Configurar Informe','configurar_informe','VentanaGenerarReporteTercero()','Btn_configurar_informe_clientes');

	$informe->InformeExportarPDF	= 	"false";	//SI EXPORTA A PDF
	$informe->InformeExportarXLS	= 	"flase";	//SI EXPORTA A XLS

	$informe->InformeTamano = "CARTA-HORIZONTAL";
	// CHANGE CSS
	$informe->DefaultCls            = 	''; 		//RESET STYLE CSS
	$informe->HeightToolbar         = 	80; 		//HEIGHT TOOLBAR
	$informe->AreaInformeQuitaAncho = 0;
	$informe->AreaInformeQuitaAlto  = 170;
	if($modulo=='ventas'){ $informe->AreaInformeQuitaAlto = 230; }
	if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 232;}
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
	contTercero       = 1;
	contVendedores    = 1;	

	allFuncionariosTerceros = "false";

	function generarPDF_Excel_principal(tipo_documento){

		var idFuncionarios = '';

		//ARRAY FUNCIONARIOS
		array_funcionarios_Terceros.forEach(function(valor,indice,documento){
			idFuncionarios = (idFuncionarios=='')? valor : idFuncionarios+','+valor;
		});

		var resultado      ='';
		var opc_local_stor ='';
		var objEquipos     = {};
		objEquipos         = {
				tipo_tercero_reporte         : localStorage.tipo_tercero_reporte,
				clase_tercero_reporte        : localStorage.clase_tercero_reporte,				
				nombre_comercial_reporte_t   : localStorage.nombre_comercial_reporte_t,
				direccion_reporte_t          : localStorage.direccion_reporte_t,
				pais_reporte_t               : localStorage.pais_reporte_t,
				telefono1_reporte_t          : localStorage.telefono1_reporte_t,
				celular1_reporte_t           : localStorage.celular1_reporte_t,
				nombre1_reporte_t            : localStorage.nombre1_reporte_t,
				apellido1_reporte_t          : localStorage.apellido1_reporte_t,
				tercero_tributario_reporte_t : localStorage.tercero_tributario_reporte_t,
				cuidad_reporte_t             : localStorage.cuidad_reporte_t,
				departamento_reporte_t       : localStorage.departamento_reporte_t,
				telefono2_reporte_t          : localStorage.telefono2_reporte_t,
				celular2_reporte_t           : localStorage.celular2_reporte_t,
				nombre2_reporte_t            : localStorage.nombre2_reporte_t,
				apellido2_reporte_t			 : localStorage.apellido2_reporte_t,
				funcionario_asignado	     : localStorage.funcionario_asignado,
				email1	                     : localStorage.email1,
				email2	                     : localStorage.email2,
				idFuncionarios               : idFuncionarios
               };
		for (var i in objEquipos) {
		// console.log(i+"->"+objEquipos[i]);
			if (typeof(objEquipos[i])!="undefined" && typeof(objEquipos[i])!="") {
				opc_local_stor+=(opc_local_stor=='')? objEquipos[i] : i+'='+objEquipos[i]+'&';
			}
		}
		window.open("../informes/informes/crm/terceros_Result.php?"+opc_local_stor+tipo_documento+"=true");
	}

	function VentanaGenerarReporteTercero(){
<?php
		// oculta el boton de PDF si la consulta para terceros arroja mas de 500 registros
	 	$sql    = "SELECT * FROM terceros WHERE activo=1 AND tercero = 1 AND id_empresa='$id_empresa'";
	    $result = mysql_query($sql,$link);	        
?>
		Win_Ventana_reporte_tercero = new Ext.Window({
		    width       : 450,
		    height      : 350,
		    id          : 'Win_Ventana_reporte_tercero',
		    title       : 'Seleccion para los Reportes de terceros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : false,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : '../informes/informes/crm/filtros_reporte_terceros.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            prueba : 'Seleccion para los Reportes de terceros',
		        }
		    },
		    tbar        :
		    [
				{
					xtype       : 'button',
		            width       : 60,
		            height      : 56,
		            text        : 'Genera Informe',
		            scale       : 'large',
		            iconCls     : 'genera_informe',
		            iconAlign   : 'top',
		            hidden      : false,
		            handler     : function(){ BloqBtn(this); informeReporteTercero("HTML"); }
		        },'-',		        
		        {
		            xtype       : 'button',
		            width       : 60,
		            height      : 56,
		            text        : 'Exportar Excel',
		            scale       : 'large',
		            iconCls     : 'excel32',
		            iconAlign   : 'top',
		            hidden      : false,
		            handler     : function(){ BloqBtn(this); informeReporteTercero("IMPRIME_XLS"); }
		        },'-',
		        {
		            xtype       : 'button',
		            width       : 60,
		            height      : 56,
		            text        : 'Exportar CSV',
		            scale       : 'large',
		            iconCls     : 'xls32',
		            iconAlign   : 'top',
		            hidden      : false,
		            handler     : function(){ BloqBtn(this); informeReporteTercero("IMPRIME_CSV"); }
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
                    hidden      : false,
                    handler     : function(){ BloqBtn(this); Win_Ventana_reporte_tercero.close(id) }
                }
		    ]
		}).show();
	}

	function resetFiltros(){

		localStorage.MyInformeFiltroFechaFinalOrdenesCompra = "";
		localStorage.celular1_reporte_t                     = "";
		localStorage.telefono1_reporte_t                    = "";
		localStorage.tipo_tercero_reporte                   = "";
		localStorage.clase_tercero_reporte                  = "";
		localStorage.nombre_comercial_reporte_t             = "";
		localStorage.direccion_reporte_t                    = "";
		localStorage.pais_reporte_t                         = "";
		localStorage.nombre1_reporte_t                      = "";
		localStorage.apellido1_reporte_t                    = "";
		localStorage.tercero_tributario_reporte_t           = "";
		localStorage.cuidad_reporte_t                       = "";
		localStorage.departamento_reporte_t                 = "";
		localStorage.telefono2_reporte_t                    = "";
		localStorage.celular2_reporte_t                     = "";
		localStorage.nombre2_reporte_t                      = "";
		localStorage.apellido2_reporte_t                    = "";
		localStorage.funcionario_asignado                   = "";
		localStorage.email1                                 = "";
		localStorage.email2                                 = "";
		array_funcionarios_Terceros.length                  = 0;
	    funcionarios_config_Terceros.length                 = 0;		


		Win_Ventana_reporte_tercero.close();
        VentanaGenerarReporteTercero();

	}

	function informeReporteTercero(opc){
		//Captura de datos de los checkbox para armar la data y enviar por windows.open
		var tipo_tercero_reporte         = document.getElementById('tipo_tercero_reporte').value;
		var clase_tercero_reporte        = document.getElementById('clase_tercero_reporte').value;		
		var nombre_comercial_reporte_t   = document.getElementById('nombre_comercial_reporte_t').checked;
		var direccion_reporte_t          = document.getElementById('direccion_reporte_t').checked;
		var pais_reporte_t               = document.getElementById('pais_reporte_t').checked;
		var telefono1_reporte_t          = document.getElementById('telefono1_reporte_t').checked;
		var celular1_reporte_t           = document.getElementById('celular1_reporte_t').checked;
		var nombre1_reporte_t            = document.getElementById('nombre1_reporte_t').checked;
		var apellido1_reporte_t          = document.getElementById('apellido1_reporte_t').checked;
		var tercero_tributario_reporte_t = document.getElementById('tercero_tributario_reporte_t').checked;
		var cuidad_reporte_t             = document.getElementById('cuidad_reporte_t').checked;
		var departamento_reporte_t       = document.getElementById('departamento_reporte_t').checked;
		var telefono2_reporte_t          = document.getElementById('telefono2_reporte_t').checked;
		var celular2_reporte_t           = document.getElementById('celular2_reporte_t').checked;
		var nombre2_reporte_t            = document.getElementById('nombre2_reporte_t').checked;
		var apellido2_reporte_t          = document.getElementById('apellido2_reporte_t').checked;
		var funcionario_asignado         = document.getElementById('funcionario_asignado').checked;
		var email1                       = document.getElementById('email1').checked;
		var email2                       = document.getElementById('email2').checked;

		var idFuncionarios = '';

		//ARRAY FUNCIONARIOS
		array_funcionarios_Terceros.forEach(function(valor,indice,documento){
			idFuncionarios = (idFuncionarios=='')? valor : idFuncionarios+','+valor;
		});


		var data = "tipo_tercero_reporte="+tipo_tercero_reporte
					+"&clase_tercero_reporte="+clase_tercero_reporte
					+"&nombre_comercial_reporte_t="+nombre_comercial_reporte_t
					+"&direccion_reporte_t="+direccion_reporte_t
					+"&pais_reporte_t="+pais_reporte_t
					+"&telefono1_reporte_t="+telefono1_reporte_t
					+"&celular1_reporte_t="+celular1_reporte_t
					+"&nombre1_reporte_t="+nombre1_reporte_t
					+"&apellido1_reporte_t="+apellido1_reporte_t
					+"&tercero_tributario_reporte_t="+tercero_tributario_reporte_t
					+"&cuidad_reporte_t="+cuidad_reporte_t
					+"&departamento_reporte_t="+departamento_reporte_t
					+"&telefono2_reporte_t="+telefono2_reporte_t
					+"&celular2_reporte_t="+celular2_reporte_t
					+"&nombre2_reporte_t="+nombre2_reporte_t
					+"&apellido2_reporte_t="+apellido2_reporte_t
					+"&funcionario_asignado="+funcionario_asignado
					+"&email1="+email1
					+"&email2="+email2
					+"&idFuncionarios="+idFuncionarios
					+"&"+opc+"=true";
		// envio pdf y excel para que sea impreso, pdf en una ventana (iframe) y el excel se descarga directamente
		if (opc=="HTML") {
			Ext.get('RecibidorInforme_Terceros').load({
				url     : '../informes/informes/crm/terceros_Result.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc                          : "HTML",
					tipo_tercero_reporte         : document.getElementById('tipo_tercero_reporte').value,
					clase_tercero_reporte        : document.getElementById('clase_tercero_reporte').value,					
					nombre_comercial_reporte_t   : document.getElementById('nombre_comercial_reporte_t').checked,
					direccion_reporte_t          : document.getElementById('direccion_reporte_t').checked,
					pais_reporte_t               : document.getElementById('pais_reporte_t').checked,
					telefono1_reporte_t          : document.getElementById('telefono1_reporte_t').checked,
					celular1_reporte_t           : document.getElementById('celular1_reporte_t').checked,
					nombre1_reporte_t            : document.getElementById('nombre1_reporte_t').checked,
					apellido1_reporte_t          : document.getElementById('apellido1_reporte_t').checked,
					tercero_tributario_reporte_t : document.getElementById('tercero_tributario_reporte_t').checked,
					cuidad_reporte_t             : document.getElementById('cuidad_reporte_t').checked,
					departamento_reporte_t       : document.getElementById('departamento_reporte_t').checked,
					telefono2_reporte_t          : document.getElementById('telefono2_reporte_t').checked,
					celular2_reporte_t           : document.getElementById('celular2_reporte_t').checked,
					nombre2_reporte_t            : document.getElementById('nombre2_reporte_t').checked,
					apellido2_reporte_t          : document.getElementById('apellido2_reporte_t').checked,
					funcionario_asignado         : document.getElementById('funcionario_asignado').checked,	
					email1                       : document.getElementById('email1').checked,	
					email2                       : document.getElementById('email2').checked,
					idFuncionarios               : idFuncionarios

				}
			});
				localStorage.tipo_tercero_reporte         = document.getElementById('tipo_tercero_reporte').value;
				localStorage.clase_tercero_reporte        = document.getElementById('clase_tercero_reporte').value;				
				localStorage.nombre_comercial_reporte_t   = document.getElementById('nombre_comercial_reporte_t').checked;
				localStorage.direccion_reporte_t          = document.getElementById('direccion_reporte_t').checked;
				localStorage.pais_reporte_t               = document.getElementById('pais_reporte_t').checked;
				localStorage.telefono1_reporte_t          = document.getElementById('telefono1_reporte_t').checked;
				localStorage.celular1_reporte_t           = document.getElementById('celular1_reporte_t').checked;
				localStorage.nombre1_reporte_t            = document.getElementById('nombre1_reporte_t').checked;
				localStorage.apellido1_reporte_t          = document.getElementById('apellido1_reporte_t').checked;
				localStorage.tercero_tributario_reporte_t = document.getElementById('tercero_tributario_reporte_t').checked;
				localStorage.cuidad_reporte_t             = document.getElementById('cuidad_reporte_t').checked;
				localStorage.departamento_reporte_t       = document.getElementById('departamento_reporte_t').checked;
				localStorage.telefono2_reporte_t          = document.getElementById('telefono2_reporte_t').checked;
				localStorage.celular2_reporte_t           = document.getElementById('celular2_reporte_t').checked;
				localStorage.nombre2_reporte_t            = document.getElementById('nombre2_reporte_t').checked;
				localStorage.apellido2_reporte_t          = document.getElementById('apellido2_reporte_t').checked;
				localStorage.funcionario_asignado         = document.getElementById('funcionario_asignado').checked;	
				localStorage.email1                       = document.getElementById('email1').checked;
				localStorage.email2                       = document.getElementById('email2').checked;			

		}else if (opc=="IMPRIME_PDF") {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();
			Win_Ventana_reporte_terceros = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_reporte_terceros',
			    title       : 'Ver PDF Terceros',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../informes/informes/crm/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc  : 'ventana_tercerosPDF',
						data : data,
			        }
			    },
			}).show();
		}else{ window.open("../informes/informes/crm/terceros_Result.php?"+data); }
	}
</script>