<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'configuracion_actividades_crm';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'crm_configuracion_actividades';	//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo=1';	//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= 'id ASC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 900;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 400;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 20;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto			= 165;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'motivo';			//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Departamento','departamento',160);
			$grilla->AddRow('Nombre','nombre',180);	
			//$grilla->AddRow('Fecha Completa','fecha_completa',87);	
			$grilla->AddRowImage('Fecha Completa','<center><div id="divFechaCompleta_[id]"></div></center><script>if(\'[fecha_completa]\'==\'inline\'){ document.getElementById("divFechaCompleta_[id]").innerHTML = "Si"; } else{ document.getElementById("divFechaCompleta_[id]").innerHTML = "No"; }</script>','87','');				
			$grilla->AddRowImage('Fecha Vencimiento','<center><div id="divFechaVencimiento_[id]"></div></center><script>if(\'[fecha_vencimiento]\'==\'inline\'){ document.getElementById("divFechaVencimiento_[id]").innerHTML = "Si"; } else{ document.getElementById("divFechaVencimiento_[id]").innerHTML = "No"; }</script>','110','');						
			$grilla->AddRowImage('Copia CRM','<center><div id="divCopiaCRM_[id]"></div></center><script>if(\'[copiar_crm_obligatorio]\'==\'true\'){ document.getElementById("divCopiaCRM_[id]").innerHTML = "Si"; } else{ document.getElementById("divCopiaCRM_[id]").innerHTML = "No"; }</script>','70','');		
			$grilla->AddRowImage('Icono','<center><img src="../calendario/images/t[icono]B.png" style="cursor:pointer" width="16" height="16" id="configuracion_actividades_crm_imgIcon_[id]"/></center>','35');
			$grilla->AddRowImage('Genera Visita','<center><div id="divGeneraVisita_[id]"></div></center><script>if(\'[genera_visita]\'==\'true\'){ document.getElementById("divGeneraVisita_[id]").innerHTML = "Si"; } else{ document.getElementById("divGeneraVisita_[id]").innerHTML = "No"; }</script>','70','');		
			$grilla->AddRowImage('Genera Llamada','<center><div id="divGeneraLlamada_[id]"></div></center><script>if(\'[genera_llamada]\'==\'true\'){ document.getElementById("divGeneraLlamada_[id]").innerHTML = "Si"; } else{ document.getElementById("divGeneraLlamada_[id]").innerHTML = "No"; }</script>','87','');		
			
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Configuracion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 410;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 500;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 900;				//AJUSTE EN PIXELES QUE SE LE DESCUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 300;				//AJUSTE EN PIXELES QUE SE LE DESCUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 280;
			$grilla->FColumnaGeneralAncho	= 280;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 150;

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION			

	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if(!isset($opcion)){?>

<script>

	function Agregar_configuracion_actividades_crm(){		
		ventanaConfiguracionActividadCRM();
	}

	function Editar_configuracion_actividades_crm(id){		
		ventanaConfiguracionActividadCRM(id);
	}

	function ventanaConfiguracionActividadCRM(id=0){

		let buttons = [
		                {
		                    xtype       : 'button',
		                    width       : 60,
		                    height      : 56,
		                    text        : (id>0)? 'Actualizar' : 'Guardar' ,
		                    scale       : 'large',
		                    iconCls     : 'guardar',
		                    iconAlign   : 'top',
		                    hidden      : false,
		                    handler     : function(){ BloqBtn(this); guardarConfiguracionActividadCRM(id); }
		                }
		            ]

		if(id >0){
			buttons = [...buttons,
							{
								xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Eliminar' ,
			                    scale       : 'large',
			                    iconCls     : 'eliminar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ 
		                    					BloqBtn(this); 
	                    						if (!confirm("Esta seguro que quiere eliminar el registro?")){ return; }
                    							deleteActivity(id);
		                    					
	                    					}	
							}
						]
		}


		Win_Ventana_ConfigurarActividadCRM = new Ext.Window({
		    width       : 350,
		    height      : 340,
		    id          : 'Win_Ventana_ConfigurarActividadCRM',
		    title       : ((id>0)? 'Actualizar' : 'Guardar')+' Tipo de Actividad',
		    modal       : true,
		    autoScroll  : false,
		    closable    : true,
		    autoDestroy : true,
		    autoLoad    :
		    {
		        url     : 'configuracion_actividades_crm/bd/bd.php',
		        scripts : true,
		        nocache : true,
		        params  :
		        {
		            op  : 'ventanaConfiguracionActividadCRM',
		            id  : id,
		        }
		    },
		    tbar        :
		    [
		        {
		            xtype   : 'buttongroup',
		            columns : 3,
		            title   : '',
		            style   : 'border-right:none;',
		            items   : buttons
		            
		        }
		    ]
		}).show();
	}

	function guardarConfiguracionActividadCRM(id){

		let departamento_act_crm      = document.getElementById("departamento_act_crm").value	
		,	nombre_act_crm            = document.getElementById("nombre_act_crm").value	
		,	fecha_completa_act_crm    = document.getElementById("fecha_completa_act_crm").value	
		,	fecha_vencimiento_act_crm = document.getElementById("fecha_vencimiento_act_crm").value
		,	icono_act_crm             = document.getElementById("icono_act_crm").value	
		,	genera_visita_act_crm     = document.getElementById("genera_visita_act_crm").value	
		,	genera_llamada_act_crm    = document.getElementById("genera_llamada_act_crm").value	
		,	copia_act_crm             = document.getElementById("copia_act_crm").value

		if(
			departamento_act_crm == '' || 
			nombre_act_crm == '' || 
			fecha_completa_act_crm == '' || 
			fecha_vencimiento_act_crm == ''  || 
			icono_act_crm == '' || 
			genera_visita_act_crm == '' || 
			genera_llamada_act_crm == '' || 
			copia_act_crm == ''
		)
		{	alert('Faltan campos por diligenciar'); return;	}

		MyLoading('on');		

		Ext.get('render_actividades_crm').load({
			url     : 'configuracion_actividades_crm/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op                        : 'guardarConfiguracionActividadCRM',
				departamento_act_crm      : departamento_act_crm,
				nombre_act_crm            : nombre_act_crm,
				fecha_completa_act_crm    : fecha_completa_act_crm,
				fecha_vencimiento_act_crm : fecha_vencimiento_act_crm,
				icono_act_crm             : icono_act_crm,
				genera_visita_act_crm     : genera_visita_act_crm,
				genera_llamada_act_crm    : genera_llamada_act_crm,
				copia_act_crm             : copia_act_crm,											
				id                        : id,		
			}
		});

	}

	let deleteActivity = (id)=>{
		MyLoading('on');		
		Ext.get('render_actividades_crm').load({
			url     : 'configuracion_actividades_crm/bd/bd.php',
			scripts : true,
			nocache : true,
			params  :
			{
				op : 'deleteActivity',										
				id : id,		
			}
		});
	}

</script>
		
<?php } ?>