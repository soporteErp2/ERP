<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa=$_SESSION['EMPRESA'];

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nomina_prestamos_empleados_pagos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_prestamos_empleados_pagos';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = $id_empresa  AND id_prestamo=$id_prestamo ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA


		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 470;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 320;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto			= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= '';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Tipo','tipo_documento',50);
			$grilla->AddRow('Documento','tipo_documento_extendido',150);
			$grilla->AddRow('Consecutivo','consecutivo_documento',90);
			$grilla->AddRow('Valor','valor',120);
			// $grilla->AddRowImage('Naturaleza','[naturaleza]<input type="hidden" id="naturaleza_concepto_[id]" value="[naturaleza]"><input type="hidden" id="imprimir_volante_concepto_[id]" value="[imprimir_volante]"><input type="hidden" id="formula_[id]" value="[formula]">',80);



		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 120;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto   = 'false';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->VBarraBotones = 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo   = 'false';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->AddBotton('Nuevo','add','ventana_nomina_prestamos_empleados_pagos(0);');
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_ventana_abono.close();');
			$grilla->VAncho        = 320;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto         = 240;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'

			$grilla->AddComboBox('Tipo Documento','tipo_documento',150,'true','false','RC:Recibo de Caja,NC:Nota Contable');
			$grilla->AddTextField('Consecutivo','consecutivo_documento',150,'true','false');
			$grilla->AddTextField('Valor','valor',150,'true','false');
			$grilla->AddValidation('valor','numero-real');

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

		function Editar_nomina_prestamos_empleados_pagos(id){
			ventana_nomina_prestamos_empleados_pagos(id);
		}

		function ventana_nomina_prestamos_empleados_pagos(id) {
			var title =(id>0)? 'Actualizar Pago Prestamo' : 'Agregar Pago Prestamo' ;

			Win_Ventana_ventana_prestamo = new Ext.Window({
				id          : 'Win_Ventana_ventana_prestamo',
				width       : 380,
				height      : 300,
				title       : title,
				modal       : true,
				autoScroll  : false,
				closable    : true,
				autoDestroy : true,
				autoLoad    :
			    {
			        url     : 'prestamos_empleados/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc         : 'ventana_prestamo',
						id          : id,
						id_empleado : '<?php echo $id_empleado; ?>',
						id_prestamo : '<?php echo $id_prestamo; ?>',
			        }
			    }
			}).show();
		}

		function guardarActualizarPago(id) {
			var opc=(id>0)? 'actualizarPago' : 'guardarPago'
			,	tipo_documento        = document.getElementById('tipo_documento').value
			,	consecutivo_documento = document.getElementById('consecutivo_documento').value
			,	id_documento          = document.getElementById('id_documento').value
			,	abono                 = document.getElementById('abono').value
			,	observacion           = document.getElementById('observacion').value;

        	observacion = observacion.replace(/[\#\<\>\'\"]/g, '');

			if (consecutivo_documento==0 || id_documento==0 || abono==0) {
				alert("Faltan Campos Obligatorios");
				return;
			}

			Ext.get('divLoad').load({
				url     : 'prestamos_empleados/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc                   : opc,
					id                    : id,
					tipo_documento        : tipo_documento,
					consecutivo_documento : consecutivo_documento,
					id_documento          : id_documento,
					abono                 : abono,
					observacion           : observacion,
					id_empleado           : '<?php echo $id_empleado; ?>',
					id_prestamo           : '<?php echo $id_prestamo; ?>',
				}
			});
		}

		function ventanaBuscardocumentoCruce() {
			var tipo_documento = document.getElementById('tipo_documento').value
			,	title          = (tipo_documento=='RC')? 'Recibo de Caja' : 'Nota Contable' ;


			Win_Ventana_buscar_documento_cruce = new Ext.Window({
			    height      : 500,
			    width       : 510,
			    id          : 'Win_Ventana_buscar_documento_cruce',
			    title       : 'Seleccionar '+title,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			    items       :
			    [
			        {
			            closable    : false,
			            border      : false,
			            autoScroll  : true,
			            iconCls     : '',
			            bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			            items       :
			            [

			                {
			                    xtype       : "panel",
			                    id          : 'contenedor_documento_cruce',
			                    border      : false,
			                    bodyStyle   : 'background-color:<?php echo $_SESSION['COLOR_FONDO'] ?>;',
			                }
			            ],
			            tbar        :
			            [
			                {
			                    xtype   : 'buttongroup',
			                    columns : 3,
			                    title   : 'Filtro',
			                    items   :
			                    [
			                        {
			                            xtype       : 'panel',
			                            border      : false,
			                            width       : 210,
			                            height      : 56,
			                            bodyStyle   : 'background-color:rgba(255,255,255,0);',
			                            autoLoad    :
			                            {
			                                url     : '../funciones_globales/filtros/filtro_unico_sucursal.php',
			                                scripts : true,
			                                nocache : true,
			                                params  :
			                                {
												renderizaBody : 'true',
												url_render    : 'prestamos_empleados/bd/grillaBuscarDocumentoCruce.php',
												contenedor    : 'contenedor_documento_cruce',
												imprimeVarPhp : 'tipo_documento : "'+tipo_documento+'",',
			                                }
			                            }
			                        }
			                    ]
			                },
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    handler     : function(){ Win_Ventana_buscar_documento_cruce.close(id) }
			                }
			            ]
			        }
			    ]

			}).show();
		}

		function eliminarPago(id) {
			if (!confirm('Realmente desea eliminar el pago?')) {return;}
			Ext.get('divLoad').load({
				url     : 'prestamos_empleados/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc                   : 'eliminarPago',
					id                    : id,
					id_empleado           : '<?php echo $id_empleado; ?>',
					id_prestamo           : '<?php echo $id_prestamo; ?>',
				}
			});
		}

    </script>

<?php } ?>