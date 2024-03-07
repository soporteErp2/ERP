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

			$informe->InformeName			=	'informe_de_terceros_contactos';  						//NOMBRE DEL INFORME
			$informe->InformeTitle			=	'Informe de Contactos por Tercero';	//TITULO DEL INFORME

			$informe->AddBotton('Configurar Informe','configurar_informe','ventanaConfigurarInforme()','Btn_configurar_informe_contactos');

			//$informe->AddFiltroEmpresa('true','true','true','false','true');
			//$informe->FiltroClientes        = 'true';
			/*$informe->InformeEmpreSucuBode	=	'false';  //FILTRO EMPRESA, SUCURSAL, BODEGA
			$informe->InformeEmpreSucu		=	'true'; //FILTRO EMPRESA, SUCURSAL
			$informe->FiltroEmpreTodos      =   'false'; //OPCION TODOS EN EL FILTRO DE EMPRESA
			$informe->FiltroSucuTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE SUCURSAL
			$informe->FiltroBodeTodos       =   'true';  //OPCION TODOS EN EL FILTRO DE BODEGA*/
			//$informe->InformeDebug  = 'true';
			//$informe->InformeFechaInicioFin	=	'true';	 //FILTRO FECHA
			//$informe->InformeExportarPDF	= 	"true";	 //SI EXPORTA A PDF
			$informe->InformeExportarXLS	= 	"true"; //SI EXPORTA A XLS
			$informe->AreaInformeQuitaAncho		= 0;
			$informe->AreaInformeQuitaAlto		= 190;
			if($modulo=='comercial'){$informe->AreaInformeQuitaAlto = 275;}

			$informe->InformeTamano         = "CARTA-HORIZONTAL";			

			// /* COMBOX PERSONALIZADO*/
			// $consul2 = $mysql->query("SELECT id,CONCAT(codigo,' - ',nombre)AS nombre FROM configuracion_proyectos WHERE activo = 1",$link);
			// $array2 = '';
			// while($row2 = $mysql->fetch_array($consul2)){
			// 	$array2 .= '["'.$row2['id'].'","'.$row2['nombre'].'"],';
			// }
			// $informe->AddFiltro('Proyecto','Seleccione el Proyecto',trim($array2,','),0);

			//  COMBOX PERSONALIZADO
			// $consul3 = $mysql->query("SELECT id,CONCAT(codigo_proyecto,codigo,' - ',nombre)AS nombre FROM configuracion_proyectos_actividades WHERE activo = 1",$link);
			// $array3 = '';
			// while($row3 = $mysql->fetch_array($consul3)){
			// 	$array3 .= '["'.$row3['id'].'","'.$row3['nombre'].'"],';
			// }
			// $informe->AddFiltro('Actividad','Seleccione la Actividad',trim($array3,','),0);


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

	contTercero = 1;

	url = '';
	if('<?php echo $modulo; ?>' == 'comercial'){//si se accede desde compras
		url = '../informes/';
	}

	function ventanaConfigurarInforme(){

		url_1 = '';
		if('<?php echo $modulo; ?>' == 'comercial'){//si se accede desde compras
			url_1 = '../informes/';
		}

		Win_Ventana_configurar_informe_facturas = new Ext.Window({
		    width       : 670,
		    height      : 500,
		    id          : 'Win_Ventana_configurar_informe_facturas',
		    title       : 'Aplicar Filtros',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : url_1+'informes/crm/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
					opc    : 'cuerpoVentanaConfiguracionTercerosContactos',
					modulo : '<?php echo $modulo; ?>'
		        }
		    },
		    tbar        :
		    [
		    	{
                    xtype   : 'buttongroup',
                    columns : 3,
                    title   : '',
                    //height  : 200,
                    items   :
                    [
                        {
                            xtype       : 'panel',
                            border      : false,
                            width       : 260,
                            height      : 80,
                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
                            autoLoad    :
                            {
                                url     : '../informes/informes/crm/bd.php',
                                scripts : true,
                                nocache : true,
                                params  : {
									opc    : 'filtro_ubicacion',
									modulo : '<?php echo $modulo; ?>'									
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
                    handler     : function(){ generarHtml() }
                },
                // {
                //     xtype       : 'button',
                //     width       : 60,
                //     height      : 56,
                //     text        : 'Exportar<br>PDF',
                //     scale       : 'large',
                //     iconCls     : 'genera_pdf',
                //     iconAlign   : 'top',
                //     handler     : function(){ generarPDF_Excel('IMPRIME_PDF') }
                // },
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
		            handler     : function(){ Win_Ventana_configurar_informe_facturas.close() }
		        }
		    ]
		}).show();
	}

	//========================== VENTANA PARA BUSCAR LOS TERCEROS ===============================//
	function ventanaBusquedaTerceros(opc){

		var ciudad_tercero       = document.getElementById('select_ciudad').value	
		,   departamento_tercero = document.getElementById('select_departamento').value
		,   pais_tercero         = document.getElementById('select_pais').value;

		url_2 = '';
		if('<?php echo $modulo; ?>' == 'comercial'){//si se accede desde compras
			url_2 = '../informes/';
		}

		where = '';

		if(ciudad_tercero != 'todos'){
			where = ' AND id_ciudad = '+ciudad_tercero;
		}

		if(departamento_tercero != 'todos'){
			where += ' AND id_departamento = '+departamento_tercero;
		}

		if(pais_tercero != 'todos'){
			where += ' AND id_pais = '+pais_tercero;
		}
		
		if (opc=='proveedores'){
			tabla          ='terceros';
			tercero        ='nombre_comercial';
			titulo_ventana ='Proveedores';
			idEmpresa      = '';
			url_3          = url_2+'informes/crm/BusquedaProveedores.php';			
		}		

        Win_VentanaCliente_terceros = new Ext.Window({
            width       : 650,
            height      : 605,
            id          : 'Win_VentanaCliente_terceros',
            title       : titulo_ventana,
            modal       : true,
            autoScroll  : false,
            closable    : false,
            autoDestroy : true,
            autoLoad    :
            {
                url     : url_3,
                scripts : true,
                nocache : true,
                params  :
                {
					tabla                : tabla,
					id_tercero           : 'id',
					tercero              : tercero,
					opcGrillaContable 	 : 'terceros_contactos',
					cargaFuncion         : '',
					nombre_grilla        : '',
					idEmpresa			 : idEmpresa,
					modulo		   		 : '<?php echo $modulo; ?>',
					where                : where
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
                    handler     : function(){ Win_VentanaCliente_terceros.close(id) }
                }
            ]
        }).show();
	}

	//FUNCION DE LA VENTANA DE BUSQUDA DE CLIENTES Y VENDEDORES
	function checkGrilla(checkbox,cont,tabla){

		url_4 = '';
		if('<?php echo $modulo; ?>' == 'comercial'){//si se accede desde crm
			url_4 = '../informes/';
		}		

		if (checkbox.checked ==true) {

			//CREAMOS EL DIV EN LA TABLA DE CONFIGURAR

            if (tabla=='terceros') {            
            	var div   = document.createElement('div');
            	div.setAttribute('id','fila_cartera_tercero_'+cont);
            	div.setAttribute('class','filaBoleta');
            	document.getElementById('bodyTablaConfiguracionTerceros').appendChild(div);

            	//CAPTURAR LOS VALORES DE LA FILA PARA LUEGO MOSTRARLOS
            	var nit=document.getElementById('nit_'+cont).innerHTML;
            	var tercero=document.getElementById('tercero_'+cont).innerHTML;
            	//LLENAMOS EL ARRAY CON ELCLIENTE CREADO
            	var fila='<div class="campo0">'+contTercero+'</div><div class="campo1" id="nits_'+cont+'">'+nit+'</div><div class="campo2" style="width:200px;" id="terceros_'+cont+'" title="'+tercero+'">'+tercero+'</div><div class="campo4" style="width:25px;"><img src="'+url_4+'img/eliminar.png" style="margin-right:6px;margin-top:-1px;cursor:pointer;" onclick="eliminaCliente('+cont+',\''+tabla+'\')" title="Eliminar Cliente"></div>';
            	proveedoresConfiguradosTC[cont]=fila;
            	//CREAMOS LOS ELEMENTOS DEL ELEMENTO CREADO
            	document.getElementById('fila_cartera_tercero_'+cont).innerHTML=fila;
            	contTercero++;

            	//LENAMOS UN ARRAY CON INDICE EL CONT Y COMO VALOR EL ID TERCERO
            	arrayproveedoresTC[cont]=checkbox.value;
        	}

		}
		else if (checkbox.checked ==false) {			
			delete arrayproveedoresTC[cont];
			delete proveedoresConfiguradosTC[cont];
			(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));
		}


		
	}

	//============================ FUNCION PARA ELIMINAR LOS CLIENTES AGREGADOS =========================//
	function eliminaCliente(cont,tabla){		
		delete arrayproveedoresTC[cont];
		delete proveedoresConfiguradosTC[cont];
		(document.getElementById("fila_cartera_tercero_"+cont)).parentNode.removeChild(document.getElementById("fila_cartera_tercero_"+cont));				
	}

	function generarHtml(){	
		var MyInformeFiltroFechaFinal  = document.getElementById('MyInformeFiltroFechaFinal').value;
		var MyInformeFiltroFechaInicio = document.getElementById('MyInformeFiltroFechaInicio').value;		
		var idProveedores              = '';
		var ciudad_tercero             = document.getElementById('select_ciudad').value;	
		var departamento_tercero       = document.getElementById('select_departamento').value;
		var pais_tercero               = document.getElementById('select_pais').value;
		var elementos                  = document.getElementsByName('tipo_tercero');
		var tipo_tercero               = '';

		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_tercero=elementos[i].value;}
		}

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayproveedoresTC.length; i++) {
			if (typeof(arrayproveedoresTC[i])!="undefined" && arrayproveedoresTC[i]!="") {
				idProveedores=(idProveedores=='')? arrayproveedoresTC[i] : idProveedores+','+arrayproveedoresTC[i] ;
			}
		}
			

		Ext.get('RecibidorInforme_informe_de_terceros_contactos').load({
			url     : url+'informes/crm/informe_de_terceros_contactos_Result.php',
			text	: 'Generando Informe...',
			scripts : true,
			nocache : true,
			params  :
			{
				nombre_informe             : 'INFORME DE CONTACTOS POR TERCERO',		
				idProveedores              : idProveedores,		
				id_pais                    : pais_tercero,
				id_departamento            : departamento_tercero,
				id_ciudad                  : ciudad_tercero,
				tipo_tercero               : tipo_tercero,				
				con_contactos              : checkboxConContactos,
				sin_contactos              : checkboxSinContactos,
				MyInformeFiltroFechaFinal  : MyInformeFiltroFechaFinal,
				MyInformeFiltroFechaInicio : MyInformeFiltroFechaInicio,
			}
		});

		document.getElementById("RecibidorInforme_informe_de_terceros_contactos").style.padding = 20;			
		
		localStorage.tipo_tercero     = tipo_tercero;

		localStorage.MyInformeFiltroFechaFinalTC  = MyInformeFiltroFechaFinal;
		localStorage.MyInformeFiltroFechaInicioTC = MyInformeFiltroFechaInicio;

	}

	function resetFiltros(){
				
		arrayproveedoresTC.length        = 0;
		proveedoresConfiguradosTC.length = 0;
		localStorage.tipo_tercero        = "";
        checkboxConContactos             = "";
        checkboxSinContactos             = "";                 

        Win_Ventana_configurar_informe_facturas.close();

        ventanaConfigurarInforme();

	}

	function generarPDF_Excel(tipo_documento){		

		var MyInformeFiltroFechaFinal  = '' ;
		var MyInformeFiltroFechaInicio = '' ;
		var idProveedores              = '';
		var ciudad_tercero             = document.getElementById('select_ciudad').value;	
		var departamento_tercero       = document.getElementById('select_departamento').value;
		var pais_tercero               = document.getElementById('select_pais').value;
		var elementos                  = document.getElementsByName('tipo_tercero');
		var tipo_tercero               = '';
		var con_contactos              = checkboxConContactos;
		var sin_contactos              = checkboxSinContactos;


		for(var i=0; i<elementos.length; i++) {
			if (elementos[i].checked) {tipo_tercero=elementos[i].value;}
		}

		if (typeof(localStorage.MyInformeFiltroFechaInicioTC)!="undefined" && typeof(localStorage.MyInformeFiltroFechaFinalTC)!="undefined") {
			if (localStorage.MyInformeFiltroFechaInicioTC!='' && localStorage.MyInformeFiltroFechaFinalTC) {
				MyInformeFiltroFechaFinal  = localStorage.MyInformeFiltroFechaFinalTC;
				MyInformeFiltroFechaInicio = localStorage.MyInformeFiltroFechaInicioTC;
			}
		}

		//RECORREMOS EL ARRAY DE LOS CLIENTES PARA ENVIARLO A LA CONSULTA
		for ( i = 0; i < arrayproveedoresTC.length; i++) {
			if (typeof(arrayproveedoresTC[i])!="undefined" && arrayproveedoresTC[i]!="") {
				idProveedores=(idProveedores=='')? arrayproveedoresTC[i] : idProveedores+','+arrayproveedoresTC[i] ;
			}
		}	

		file = "informe_de_terceros_contactos_Result.php";		

		window.open(url+"informes/crm/"+file+"?"+tipo_documento+"=true&idProveedores="+idProveedores+"&id_ciudad="+ciudad_tercero+"&id_pais="+pais_tercero+"&id_departamento="+departamento_tercero+"&tipo_tercero="+tipo_tercero+"&MyInformeFiltroFechaFinal="+MyInformeFiltroFechaFinal+"&MyInformeFiltroFechaInicio="+MyInformeFiltroFechaInicio+"&con_contactos="+con_contactos+"&sin_contactos="+sin_contactos);

	}

</script>