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

	$empresa          = $_SESSION['EMPRESA'];
	$filtro_sucursal  = $_SESSION['SUCURSAL'];

    $sql="SELECT id_planilla FROM nomina_planillas_empleados WHERE activo=1 AND id_empresa=$empresa GROUP BY id_planilla";
    $query=mysql_query($sql,$link);
    $whereId='id=0';
    while ($row=mysql_fetch_array($query)) {
        $whereId.=($whereId=='')? 'id='.$row['id_planilla'] :' OR id='.$row['id_planilla'];
    }

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nomina_planillas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_planillas';		//NOMBRE DE LA TABLA DE CONSULTA EN LA BASE DE DATOS DE
			$grilla->MyWhere			= 'activo = 1 AND id_empresa='.$empresa.' AND ('.$whereId.' OR consecutivo>0)';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy 			= 'consecutivo DESC';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			//$grilla->Ancho		 	= 800;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->Alto		 		= 220;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->QuitarAncho		= 25;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			$grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'fecha_inicio,fecha_final,consecutivo,usuario,sucursal';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Fecha Inicial','fecha_inicio',100);
			$grilla->AddRow('Fecha Final','fecha_final',100);
            $grilla->AddRow('Consecutivo','consecutivo',100);
			$grilla->AddRow('Tipo Pago','tipo_liquidacion',150);
			$grilla->AddRow('Usuario','usuario',250);
			$grilla->AddRowImage('Estado','<center><img src="img/estado_doc/[estado].png" id="imgEstado_[id]"  style="cursor:pointer" width="16" height="16"/></center>','50');
            $grilla->AddRow('Sucursal','sucursal',150);


			$grilla->FContenedorAncho		= 500;
			$grilla->FColumnaGeneralAncho	= 250;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 250;
			$grilla->FColumnaFieldAncho		= 25;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto            = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana          = 'Ventana Inventario'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->CerrarDespuesDeAgregar = 'false';
			$grilla->VBarraBotones          = 'false';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo            = 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText            = 'Nuevo Item'; 	//TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage           = 'addequipo';	    //IMAGEN CSS DEL BOTON
			//$grilla->VAutoResize          = 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho                 = 560;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VAlto                = 570;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			//$grilla->VQuitarAncho         = 540;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto            = 20;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll            = 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar         = 'false';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar        = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DEL MENU CONTEXTUAL
 			// $grilla->MenuContext		= 'true';		//MENU CONTEXTUAL
	 		// $grilla->MenuContextEliminar= 'false';

		//OPCIONES ADICIONALES EN EL MENU CONTEXTUAL
 			if (user_permisos(51,'false') == 'true') $grilla->AddMenuContext('Traslado de Inventario','doc','ventana_traslado([id])');
			// $grilla->AddMenuContext('Imprimir Codigo de Barras','barcode16','ventana_codigo_barras([id])');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/



if(!isset($opcion)){ ?>

	<script>

		function Editar_nomina_planillas(id){
			AbreVentanaGrillaPlanilla(id);
		}

		function Agregar_nomina_planillas (argument) {
			AbreVentanaGrillaPlanilla('');
		}


		//ABRIR LA VENTANA PARA AGREGAR O EDITAR LA PLANILLA
    	function AbreVentanaGrillaPlanilla(id_planilla){
    		var opcGrillaContable='NominaPlanilla';
            var  direccionRender = 'planilla/grillaPlanilla.php';
            if (id_planilla>0) {
                var  estado          = document.getElementById('imgEstado_'+id_planilla).getAttribute('src')
                var  direccionRender = (estado=='img/estado_doc/1.png' || estado=='img/estado_doc/3.png')? 'planilla/bd/grillaContableBloqueada.php' : 'planilla/grillaPlanilla.php' ;
            }

        	var myalto2  = Ext.getBody().getHeight();
        	var myancho2 = Ext.getBody().getWidth();

        	WinAlto = myalto2-20;

        	WinAncho = myancho2-30;

        	Win_ventana_planilla = new Ext.Window({
                width       : WinAncho,
                height      : WinAlto,
                title       : 'Planilla de Nomina',
                modal       : true,
                autoScroll  : false,
                autoDestroy : false,
                closable    : false,
                bodyStyle   : 'background-color:#fff;',
                id          : 'Win_ventana_planilla',
                autoLoad    :
                                {
                                    url     : direccionRender,
                                    scripts : true,
                                    nocache : true,
                                    params  :
                                            {
                                                opcGrillaContable : opcGrillaContable,
                                                id_planilla       : id_planilla,

                                            }
                                },
                tbar        :
                        [

                            {
                                xtype   : 'buttongroup',
                                id      : 'BtnGroup_Guardar_'+opcGrillaContable,
                                height  : 80,
                                style   : 'border:none;',
                                columns : 1,
                                title   : 'Generar',
                                items   :
                                [
                                    {
                                        xtype       : 'button',
                                        width       : 60,
                                        height      : 56,
                                        text        : 'Guardar',
                                        tooltip     : 'Generar Planilla',
                                        id          : 'Btn_guardar_'+opcGrillaContable,
                                        scale       : 'large',
                                        iconCls     : 'guardar',
                                        iconAlign   : 'top',
                                        // disabled    : true,
                                        handler     : function(){ BloqBtn(this); guardarPlanilla() }
                                    }
                                ]
                            },
                            {
                                xtype   : 'buttongroup',
                                height  : 80,
                                id      : 'BtnGroup_Cancelar_'+opcGrillaContable,
                                style   : 'border:none;',
                                columns : 9,
                                title   : 'Opciones',
                                items   :
                                [

                                    {
                                        xtype       : 'button',
                                        width       : 60,
                                        height      : 56,
                                        id          : 'Btn_cancelar_'+opcGrillaContable,
                                        text        : 'Cancelar',
                                        tooltip     : 'Cancelar Planilla',
                                        scale       : 'large',
                                        iconCls     : 'cancel',
                                        iconAlign   : 'top',
                                        handler     : function(){ BloqBtn(this); cancelarPlanillaNomina(); }
                                    }
                                ]
                            },'-',
                            {
                                xtype   : 'buttongroup',
                                height  : 80,
                                id      : 'BtnGroup_Estado1_'+opcGrillaContable,
                                columns : 4,
                                title   : 'Documento Generado',
                                items   :
                                [
                                    {
                                        xtype       : 'button',
                                        id          : 'Btn_exportar_'+opcGrillaContable,
                                        width       : 60,
                                        height      : 56,
                                        text        : 'Imprimir',
                                        tooltip     : 'Imprimir en un documento PDF',
                                        scale       : 'large',
                                        iconCls     : 'pdf32_new',
                                        iconAlign   : 'top',
                                        handler     : function(){ BloqBtn(this); imprimirPlanillaNomina(); }

                                    },
                                    {
                                        xtype       : 'button',
                                        id          : 'Btn_editar_'+opcGrillaContable,
                                        width       : 60,
                                        height      : 56,
                                        text        : 'Editar',
                                        tooltip     : 'Editar Planilla',
                                        scale       : 'large',
                                        iconCls     : 'edit',
                                        iconAlign   : 'top',
                                        hidden : false,
                                        handler     : function(){ BloqBtn(this); modificarDocumentoPlanillaNomina(); }
                                    },
                                    {
                                        xtype       : 'button',
                                        id          : 'Btn_restaurar_'+opcGrillaContable,
                                        width       : 60,
                                        height      : 56,
                                        text        : 'Restaurar',
                                        tooltip     : 'Restaurar Planilla',
                                        scale       : 'large',
                                        iconCls     : 'restaurar32',
                                        iconAlign   : 'top',
                                        handler     : function(){ BloqBtn(this); restaurarPlanillaNomina(); }
                                    }
                                ]
                            },
                            {
                                xtype   : 'buttongroup',
                                id      : '',
                                height  : 80,
                                style   : 'border:none;',
                                columns : 1,
                                title   : 'Cerrar',
                                items   :
                                [
                                    {
                                        xtype       : 'button',
                                        width       : 60,
                                        height      : 56,
                                        text        : 'Regresar',
                                        id          : '',
                                        scale       : 'large',
                                        iconCls     : 'regresar',
                                        iconAlign   : 'top',
                                        // disabled    : true,
                                        handler     : function(){ cerrarPlanilla() }
                                    }
                                ]
                            },

                            '->',
                            {
                                xtype : "tbtext",
                                text  : '<div id="titleDocumento'+opcGrillaContable+'" style="text-align:center; font-size:18px; font-weight:bold;"></div>',
                                scale : "large",
                            }
                        ]


        	}).show();
    	}

</script>

<?php
} ?>


