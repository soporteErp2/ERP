<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa = $_SESSION['EMPRESA'];

	// CONSULTAR LOS CONCEPTOS, SI HAY TIPO PROVISION, MOSTRAR LA COLUMNA DE CONFIGURACION DE LA BASE
	$sql="SELECT COUNT(id) AS cont FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id_grupo=$id_grupo AND naturaleza='Provision'";
	$query=mysql_query($sql,$link);
	$cont=mysql_result($query,0,'cont');


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/


	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nominaConceptos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_conceptos';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_grupo=$id_grupo";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->OrderBy			= '';
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 610;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 465;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 150;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 265;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo,descripcion,naturaleza,caracter,caracter_contrapartida,cuenta_colgaap,cuenta_niif,cuenta_contrapartida_colgaap,cuenta_contrapartida_niif';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Codigo','codigo',80);
			// $grilla->AddValidation('codigo','numero');
			$grilla->AddValidation('codigo','unico_global',' id_empresa='.$id_empresa);
			$grilla->AddRow('Descripcion','descripcion',250);
			$grilla->AddRow('Tipo ','tipo_concepto',55);
			$grilla->AddRow('Naturaleza','naturaleza',70);
			$grilla->AddRowImage('G. Trabajo','<center title="Configurar Cuentas Grupos de Trabajo"><img src="img/user_suit.png" style="cursor:pointer" width="16" height="16" onclick="ventana_configuracion_grupos_trabajo(\'[id]\',\'[descripcion]\')"></center><div style="display:none;" id="div_nominaConceptos_id_cuenta_colgaap_[id]">[id_cuenta_colgaap]</div><div style="display:none;" id="div_nominaConceptos_id_cuenta_niif_[id]">[id_cuenta_niif]</div><div style="display:none;" id="div_nominaConceptos_id_cuenta_contrapartida_colgaap_[id]">[id_cuenta_contrapartida_colgaap]</div><div style="display:none;" id="div_nominaConceptos_id_cuenta_contrapartida_niif_[id]">[id_cuenta_contrapartida_niif]</div><div style="display:none;" id="div_nominaConceptos_centro_costos_[id]">[centro_costos]</div><div style="display:none;" id="div_nominaConceptos_centro_costos_contrapartida_[id]">[centro_costos_contrapartida]</div><div style="display:none;" id="div_nominaConceptos_nivel_formula_[id]">[nivel_formula]</div><div style="display:none;" id="div_nominaConceptos_formula_[id]">[formula]</div><div style="display:none;" id="div_nominaConceptos_concepto_ajustable_[id]">[concepto_ajustable]</div>',60);

			if ($cont>0) {
				$grilla->AddRowImage('Conf. Base','<center title="configuracion Liquidacion"><img src="img/config16.png" style="cursor:pointer;" onclick="ventana_configuracion_conceptos_liquidacion(\'[id]\',\'[descripcion]\')"></center>',65);
			}

			$grilla->AddRow('Cuenta Colgaap','cuenta_colgaap',100);
			$grilla->AddRow('Cuenta Niif','cuenta_niif',100);
			$grilla->AddRow('Caracter','caracter',70);
			$grilla->AddRow('Cuenta Cruce Colgaap','cuenta_contrapartida_colgaap',130);
			$grilla->AddRow('Cuenta Cruce Niif','cuenta_contrapartida_niif',100);
			$grilla->AddRow('Caracter','caracter_contrapartida',70);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 310;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Concepto Nomina'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nuevo Concepto'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addcontactos';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_definicion_tributaria.close();Actualiza_Div_nominaGruposConceptos('.$id_grupo.');');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 380;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 500;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Datos Centro De Costos');
			$grilla->AddSeparator('General');
			$grilla->AddTextField('Codigo:','codigo',180,'true','false');
            $grilla->AddValidation('codigo','mayuscula');
			$grilla->AddTextField('Descripcion:','descripcion',180,'true','false');
            $grilla->AddValidation('descripcion','mayuscula');
			$grilla->AddTextField('','id_empresa',180,'true','true',$id_empresa);
			$grilla->AddTextField('','id_grupo',180,'true','true',$id_grupo);
			$grilla->AddTextField('','grupo',180,'true','true',$grupo);
			$grilla->AddComboBox('Tipo Concepto:','tipo_concepto',180,'true','false','General:General,Personal:Personal');
			$grilla->AddComboBox('Concepto de Ajuste:','concepto_ajustable',180,'true','false','true:Si,false:No');
			/**
			 * la clasificacion corresponde al tipo de concepto para armar el json de la nomina electronica
			 * hora_extra_diurna : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * hora_extra_nocturna : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * hora_recargo_nocturno : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * hora_recargo_diario_dominicales_y_festivas : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * hora_extra_nocturna_dominicales_y_festivas : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * hora_recargo_nocturno_dominicales_y_festivas : todas las horas extras deben tener feha y hora de inicio y hora y fecha de fin, valor y porcentaje
			 * vacaciones : si es vacaciones
			 * prima : si es vacaciones
			 * cesantias : se debe ingresar de forma manual el valor del interes de cesantias
			 * incapacidad : se debe ingresar de forma manual fechas, cantidad, tipo que puede ser (1:Comun,2:Profesional,3:Laboral) y el valor
			 * licencia_maternidad_paternidad
			 * licencia_remunerada
			 * licencia_no_remunerada
			 * salario
			 * auxilio_transporte
			 * viaticos_salariales
			 * viaticos_no_salariales
			 * bonififacion_salarial
			 * bonififacion_no_salarial
			 * auxilio_salarial
			 * auxilio_no_salarial
			 * comision_salarial
			 * comision_no_salarial
			 * pago_terceros
			 * pago_terceros_no_salarial
			 * anticipo_salarial
			 * anticipo_no_salarial
			 * dotacion
			 * apoyo_sostenimiento
			 * teletrabajo
			 * bonificacion_retiro
			 * indemnizacion
			 * reintegro
			 * salud
			 * pension
			 * fondo_solidaridad_pensional
			 * libranza
			 * otras_deducciones
			 * pension_voluntaria
			 * retefuente
			 * AFC
			 * cooperativa
			 * embargo_fiscal
			 * eps_plan_complementario
			 * educacion
			 * deuda
			 */
			
			$grilla->AddComboBox('Clasificacion:','clasificacion',180,'false','false','salario:Salario,auxilio_transporte:Auxilio transporte,viaticos_salariales:Viaticos salariales,viaticos_no_salariales:Viaticos no salariales,hora_extra_diurna:Hora extra diurna,hora_extra_nocturna:Hora extra nocturna,hora_recargo_nocturno:Hora recargo nocturno,hora_recargo_diario_dominicales_y_festivas:Hora recargo diario dominical y festivo,hora_extra_nocturna_dominicales_y_festivas:Hora extra nocturna dominical y festiva,hora_recargo_nocturno_dominicales_y_festivas:Hora recargo nocturno dominical y festivo,vacaciones:Vacaciones,prima:Prima,cesantias:Cesantias,intereses_de_cesantias:Intereses de Cesantias,incapacidad:Incapacidad,licencia_maternidad_paternidad:Licencia Maternidad/Paternidad,licencia_remunerada:Licencia remunerada,licencia_no_remunerada:Licencia no remunerada,bonififacion_salarial:Bonificacion Salarial,bonififacion_no_salarial:Bonificacion no salarial,auxilio_salarial:Auxilio salarial,auxilio_no_salarial:Auxilio no salarial,comision_salarial:Comision salarial,comision_no_salarial:Comision no salarial,pago_terceros:Pago terceros,pago_terceros_no_salarial:Pago terceros no salarial,anticipo_salarial:Anticipo salarial,anticipo_no_salarial:Anticipo no alarial,dotacion:Dotacion,apoyo_sostenimiento:Apoyo de sostenimiento:teletrabajo:Teletrabajo,bonificacion_retiro:Bonificacion retiro,indemnizacion:Indemnizacion,reintegro:Reintegro,salud:Salud,pension:Pension,fondo_solidaridad_pensional:Fondo de solidaridad pensional,libranza:Libranza,otras_deducciones:Otras deducciones,pension_voluntaria:Pension voluntaria,retefuente:Retencion en la fuente,AFC:AFC,cooperativa:Cooperativa,embargo_fiscal:Embargo Fiscal,eps_plan_complementario:Plan conplementario de salud,educacion:Educacion,deuda:Deuda');

			// $grilla->AddTextField('Tercero Cruce:','tercero',180,'true','false');
			// $grilla->AddTextField('','id_tercero',180,'false','true');

			$grilla->AddSeparator('Configuracion en la Planilla');
			$grilla->AddComboBox('Carga Automatico:','carga_automatica',180,'true','false','true:Si,false:No');
			$grilla->AddComboBox ('Naturaleza:','naturaleza',180,'true','false','Devengo:Devengo(+),Deduccion:Deduccion(-),Apropiacion:Apropiacion(),Provision:Provision()');
			$grilla->AddComboBox('Imprimir en volante:','imprimir_volante',180,'true','false','true:Si,false:No');

			$grilla->AddSeparator('Configuracion Cuentas');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap',180,'true');
			$grilla->AddTextField('','id_cuenta_colgaap',180,'true','true');
			$grilla->AddTextField('Cuenta NIIF:','cuenta_niif',180,'true');
			$grilla->AddTextField('','id_cuenta_niif',180,'true','true');
			$grilla->AddComboBox('Caracter:','caracter',180,'true','false','debito:Debito,credito:Credito');
			$grilla->AddComboBox('Tercero:','tercero',180,'true','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddComboBox('Centro de Costos:','centro_costos',180,'true','false','false:No,true:Si');

			$grilla->AddSeparator('Configuracion Cuentas Contrapartida');
			$grilla->AddTextField('Cuenta Cruce Colgaap:','cuenta_contrapartida_colgaap',180,'true');
			$grilla->AddTextField('','id_cuenta_contrapartida_colgaap',180,'true','true');
			$grilla->AddTextField('Cuenta Cruce NIIF:','cuenta_contrapartida_niif',180,'true');
			$grilla->AddTextField('','id_cuenta_contrapartida_niif',180,'true','true');
			$grilla->AddComboBox('Caracter:','caracter_contrapartida',180,'true','false','debito:Debito,credito:Credito');
			$grilla->AddComboBox('Tercero:','tercero_cruce',180,'true','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddComboBox('Centro de Costos:','centro_costos_contrapartida',180,'true','false','false:No,true:Si');

			$grilla->AddSeparator('Cuenta Liquidacion');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap_liquidacion',180,'true');
			$grilla->AddTextField('','id_cuenta_colgaap_liquidacion',180,'true','true');
			$grilla->AddTextField('Cuenta Cruce NIIF:','cuenta_niif_liquidacion',180,'true');
			$grilla->AddTextField('','id_cuenta_niif_liquidacion',180,'true','true');
			$grilla->AddComboBox('Tercero:','tercero_cruce_liquidacion',180,'true','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddTextField('Formula de liquidacion:','formula_liquidacion',180,'false');
			$grilla->AddTextField('','nivel_formula_liquidacion',180,'false','true');

			$grilla->AddSeparator('Cuenta Ajuste');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap_ajuste',180,'false');
			$grilla->AddTextField('','id_cuenta_colgaap_ajuste',180,'false','true');
			$grilla->AddTextField('Cuenta NIIF:','cuenta_niif_ajuste',180,'false');
			$grilla->AddTextField('','id_cuenta_niif_ajuste',180,'false','true');
			$grilla->AddComboBox('Tercero:','tercero_ajuste',180,'false','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddComboBox('Centro de Costos:','centro_costos_ajuste',180,'false','false','false:No,true:Si');

			$grilla->AddSeparator('Configuracion Formula');
			$grilla->AddTextField('Formula del concepto:','formula',180,'false');
			// $grilla->AddTextField('Base:','base',180,'false');
			$grilla->AddTextField('','nivel_formula',180,'false','true');
			$grilla->AddComboBox('Variable CT resta dias laborales?:','resta_dias',180,'false','false','false:No,true:Si');


	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		//function Agregar_nominaConceptos(){ ventana_concepto_nomina(0); }
		//function Editar_nominaConceptos(id){ ventana_concepto_nomina(id); }

		function ventana_concepto_nomina(id){
			var textBtn   = 'Guardar'
			,	textTitle = 'Nuevo Centro de Costo'
			,   style 	  = true;

			if(id > 0){
				textBtn   = 'actualizar';
				textTitle = 'Actualizar Centro de Costo';
				style     = false;
			}

			Win_Ventana_centro_costo = new Ext.Window({
			    width       : 250,
			    height      : 170,
			    id          : 'Win_Ventana_centro_costo',
			    title       : textTitle,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  : { opc : 'ventana_concepto_nomina', id : id }
			    },
			    tbar        :
			    [
			        {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : textBtn,
	                    scale       : 'large',
	                    iconCls     : 'guardar',
	                    iconAlign   : 'top',
	                    handler     : function(){ btnnominaConceptos(id); }
	                },
	                {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'eliminar',
	                    scale       : 'large',
	                    iconCls     : 'eliminar',
	                    iconAlign   : 'top',
	                    hidden      : style,
	                    handler     : function(){ eliminarnominaConceptos(id); }
	                },
	                {
	                    xtype       : 'button',
	                    width       : 60,
	                    height      : 56,
	                    text        : 'Regresar',
	                    scale       : 'large',
	                    iconCls     : 'regresar',
	                    iconAlign   : 'top',
	                    handler     : function(){ Win_Ventana_centro_costo.close(id) }
	                }
			    ]
			}).show();
		}

		function validateNumberInt(input){
			var patron = /[^\d]/g;
		    if(patron.test(input.value)){ input.value = (input.value).replace(patron, ''); }

		    return true;
		}

		function btnnominaConceptos(id){
			var patronNumber = /[^\d]/g
			,	patronString = /[^a-zA-Z\d]/g
			,	codigo       = document.getElementById('codigo').value
			,	nombre       = document.getElementById('descripcion').value;

			codigo = codigo.replace(patronNumber, '');
			nombre = nombre.replace(patronString, '');

			if(codigo.length == 0){ alert('Aviso.\nEl campo Codigo es obligatorio'); return; }
			else if(codigo.length%2 == 1){ alert('Aviso.\nEl campo Codigo debe tener cantidad de digitos pares'); return; }
			else if(nombre.length == 0){ alert('Aviso.\nEl campo Nombre es obligatorio'); return; }

			if(id > 0) action = 'update';

			Ext.get('render_centro_costo').load({
				url     : 'nomina_conceptos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'save_update_centro_costo',
					id     : id,
					codigo : codigo,
					nombre : nombre
				}
			});
		}

		function eliminarnominaConceptos(id) {
			if (confirm("Advertencia\nSi elimina un Centro Costos se eliminara todos los demas asociados\nDesea continuar?")) {
				Ext.get('render_centro_costo').load({
					url     : 'nomina_conceptos/bd/bd.php',
					scripts : true,
					nocache : true,
					params  :
					{
						opc    : 'eliminar_centro_costo',
						id     : id
					}
				});
			}
		}


		function ventanaBuscarPucCuentasPago(typeCuenta,campoId,campoText){

			var titulo=(typeCuenta=='colgaap')? 'Buscar Cuenta Colgaap' : 'Buscar Cuenta Niif' ;

			Win_VentanaBuscarPucCuentasPago = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucCuentasPago',
	            title       : titulo,
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : '../funciones_globales/grillas/BuscarCuentaPuc.php',
	                scripts : true,
	                nocache : true,
	                params  :
	                		{
								opc          : typeCuenta,
								nombreGrilla : 'buscar_cuenta_concepto',
								cargaFuncion : 'renderizaResultadoVentanaBuscarCuenta(id,"'+campoId+'","'+campoText+'")',
                			}
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarPucCuentasPago.close(); }
	                }
	            ]
	        }).show();
		}

		function renderizaResultadoVentanaBuscarCuenta(id,campoId,campoText){
			var cuenta = document.getElementById('div_buscar_cuenta_concepto_cuenta_'+id).innerHTML;
			document.getElementById(campoId).value=id;
			document.getElementById(campoText).value=cuenta;
			// if (typeCuenta=='colgaap') {
			// 	document.getElementById('nominaConceptos_id_cuenta_colgaap').value=id;
			// 	document.getElementById('nominaConceptos_cuenta_colgaap').value=cuenta;
			// }
			// else{
			// 	document.getElementById('nominaConceptos_id_cuenta_niif').value=id;
			// 	document.getElementById('nominaConceptos_cuenta_niif').value=cuenta;
			// }
			Win_VentanaBuscarPucCuentasPago.close();
		}

		// FUNCION PARA LA VENTANA DE LA CONFIGURACION DE LAS CUENTAS DE LOS GRUPOS DE TRABAJO
		function ventana_configuracion_grupos_trabajo(id,descripcion){
			// CAPTURAR LOS VALORES DE LAS CUENTAS
			var id_cuenta_colgaap               = document.getElementById('div_nominaConceptos_id_cuenta_colgaap_'+id).innerHTML;
			var cuenta_colgaap                  = document.getElementById('div_nominaConceptos_cuenta_colgaap_'+id).innerHTML;
			var id_cuenta_niif                  = document.getElementById('div_nominaConceptos_id_cuenta_niif_'+id).innerHTML;
			var cuenta_niif                     = document.getElementById('div_nominaConceptos_cuenta_niif_'+id).innerHTML;
			var caracter                        = document.getElementById('div_nominaConceptos_caracter_'+id).innerHTML;
			var centro_costos                   = document.getElementById('div_nominaConceptos_centro_costos_'+id).innerHTML;

			var id_cuenta_contrapartida_colgaap = document.getElementById('div_nominaConceptos_id_cuenta_contrapartida_colgaap_'+id).innerHTML;
			var cuenta_contrapartida_colgaap    = document.getElementById('div_nominaConceptos_cuenta_contrapartida_colgaap_'+id).innerHTML;
			var id_cuenta_contrapartida_niif    = document.getElementById('div_nominaConceptos_id_cuenta_contrapartida_niif_'+id).innerHTML;
			var cuenta_contrapartida_niif       = document.getElementById('div_nominaConceptos_cuenta_contrapartida_niif_'+id).innerHTML;
			var caracter_contrapartida          = document.getElementById('div_nominaConceptos_caracter_contrapartida_'+id).innerHTML;
			var centro_costos_contrapartida     = document.getElementById('div_nominaConceptos_centro_costos_contrapartida_'+id).innerHTML;

			var nivel_formula    = document.getElementById('div_nominaConceptos_nivel_formula_'+id).innerHTML;
			var naturaleza       = document.getElementById('div_nominaConceptos_naturaleza_'+id).innerHTML;
			var formula_concepto = document.getElementById('div_nominaConceptos_formula_'+id).innerHTML;

			var concepto_ajustable = document.getElementById('div_nominaConceptos_concepto_ajustable_'+id).innerHTML;
			console.log(concepto_ajustable);
			Win_Ventana_configuracion_grupos_trabajo = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_configuracion_grupos_trabajo',
			    title       : 'Configuracion Cuentas por grupo de trabajo del concepto '+descripcion,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/nomina_conceptos_grupos_trabajo.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_concepto                     : id,
						concepto                        : descripcion,
						id_cuenta_colgaap               : id_cuenta_colgaap,
						cuenta_colgaap                  : cuenta_colgaap,
						id_cuenta_niif                  : id_cuenta_niif,
						cuenta_niif                     : cuenta_niif,
						id_cuenta_contrapartida_colgaap : id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap    : cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif    : id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif       : cuenta_contrapartida_niif,
						caracter                        : caracter,
						caracter_contrapartida          : caracter_contrapartida,
						centro_costos                   : centro_costos,
						centro_costos_contrapartida     : centro_costos_contrapartida,
						nivel_formula                   : nivel_formula,
						naturaleza                      : naturaleza,
						formula_concepto                : formula_concepto,
						concepto_ajustable              : concepto_ajustable,

			        }
			    }
			}).show();
		}

		// FUNCION PARA LA VENTANA DE LA CONFIGURACION DE LOS CONCEPTOS QUE SE INCLUYEN EN LA LIQUIDACION
		function ventana_configuracion_conceptos_liquidacion(id,descripcion){
			// CAPTURAR LOS VALORES DE LAS CUENTAS
			var id_cuenta_colgaap               = document.getElementById('div_nominaConceptos_id_cuenta_colgaap_'+id).innerHTML;
			var cuenta_colgaap                  = document.getElementById('div_nominaConceptos_cuenta_colgaap_'+id).innerHTML;
			var id_cuenta_niif                  = document.getElementById('div_nominaConceptos_id_cuenta_niif_'+id).innerHTML;
			var cuenta_niif                     = document.getElementById('div_nominaConceptos_cuenta_niif_'+id).innerHTML;
			var caracter                        = document.getElementById('div_nominaConceptos_caracter_'+id).innerHTML;
			var centro_costos                   = document.getElementById('div_nominaConceptos_centro_costos_'+id).innerHTML;

			var id_cuenta_contrapartida_colgaap = document.getElementById('div_nominaConceptos_id_cuenta_contrapartida_colgaap_'+id).innerHTML;
			var cuenta_contrapartida_colgaap    = document.getElementById('div_nominaConceptos_cuenta_contrapartida_colgaap_'+id).innerHTML;
			var id_cuenta_contrapartida_niif    = document.getElementById('div_nominaConceptos_id_cuenta_contrapartida_niif_'+id).innerHTML;
			var cuenta_contrapartida_niif       = document.getElementById('div_nominaConceptos_cuenta_contrapartida_niif_'+id).innerHTML;
			var caracter_contrapartida          = document.getElementById('div_nominaConceptos_caracter_contrapartida_'+id).innerHTML;
			var centro_costos_contrapartida     = document.getElementById('div_nominaConceptos_centro_costos_contrapartida_'+id).innerHTML;

			var nivel_formula    = document.getElementById('div_nominaConceptos_nivel_formula_'+id).innerHTML;
			var naturaleza       = document.getElementById('div_nominaConceptos_naturaleza_'+id).innerHTML;
			var formula_concepto = document.getElementById('div_nominaConceptos_formula_'+id).innerHTML;

			if (naturaleza!='Provision') {
				alert("Aviso\nSolo los conceptos de naturaleza provision tienen esta cnofiguracion");
				return;
			}

			Win_Ventana_configuracion_conceptos_liquidacion = new Ext.Window({
			    width       : 650,
			    height      : 600,
			    id          : 'Win_Ventana_configuracion_conceptos_liquidacion',
			    title       : 'Configuracion de la base para la liquidacion de '+descripcion,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/nomina_configurar_base.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_concepto                     : id,
						concepto                        : descripcion,
						id_cuenta_colgaap               : id_cuenta_colgaap,
						cuenta_colgaap                  : cuenta_colgaap,
						id_cuenta_niif                  : id_cuenta_niif,
						cuenta_niif                     : cuenta_niif,
						id_cuenta_contrapartida_colgaap : id_cuenta_contrapartida_colgaap,
						cuenta_contrapartida_colgaap    : cuenta_contrapartida_colgaap,
						id_cuenta_contrapartida_niif    : id_cuenta_contrapartida_niif,
						cuenta_contrapartida_niif       : cuenta_contrapartida_niif,
						caracter                        : caracter,
						caracter_contrapartida          : caracter_contrapartida,
						centro_costos                   : centro_costos,
						centro_costos_contrapartida     : centro_costos_contrapartida,
						nivel_formula                   : nivel_formula,
						naturaleza                      : naturaleza,
						formula_concepto                : formula_concepto,

			        }
			    }
			}).show();
		}


	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>
		if ('<?php echo $opcion; ?>'=='Vagregar') {document.getElementById('nominaConceptos_concepto_ajustable').value='false';}
		if ('<?php echo $opcion; ?>'=='Vupdate') {
			if (document.getElementById('nominaConceptos_tercero_ajuste').value==0) {document.getElementById('nominaConceptos_tercero_ajuste').value='Empleado'}
			if (document.getElementById('nominaConceptos_centro_costos_ajuste').value==0) {document.getElementById('nominaConceptos_centro_costos_ajuste').value='No'}
		}

		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES
		var inputColgaap              = document.getElementById('nominaConceptos_cuenta_colgaap');
		var inputNiif                 = document.getElementById('nominaConceptos_cuenta_niif');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES CONTRAPARTIDA
		var inputContrapartidaColgaap = document.getElementById('nominaConceptos_cuenta_contrapartida_colgaap');
		var inputContrapartidaNiif    = document.getElementById('nominaConceptos_cuenta_contrapartida_niif');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES PARA LA LIQUIDACION
		var inputColgaapLiquidacion   = document.getElementById('nominaConceptos_cuenta_colgaap_liquidacion');
		var inputNiifLiquidacion      = document.getElementById('nominaConceptos_cuenta_niif_liquidacion');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES PARA EL AJUSTE
		var inputColgaapAjuste   = document.getElementById('nominaConceptos_cuenta_colgaap_ajuste');
		var inputNiifAjuste      = document.getElementById('nominaConceptos_cuenta_niif_ajuste');

		inputColgaap.readOnly              = true;
		inputNiif.readOnly                 = true;
		//CONTRAPARTIDA
		inputContrapartidaColgaap.readOnly = true;
		inputContrapartidaNiif.readOnly    = true;
		//LIQUIDACION
		inputColgaapLiquidacion.readOnly   = true;
		inputNiifLiquidacion.readOnly      = true;
		//AJUSTE
		inputColgaapAjuste.readOnly   = true;
		inputNiifAjuste.readOnly      = true;

		inputColgaap.setAttribute("style","float:left; width:135px;");
		inputNiif.setAttribute("style","float:left; width:158px;");
		//CONTRAPARTIDA
		inputContrapartidaColgaap.setAttribute("style","float:left; width:135px;");
		inputContrapartidaNiif.setAttribute("style","float:left; width:158px;");
		//LIQUIDACION
		inputColgaapLiquidacion.setAttribute("style","float:left; width:135px;");
		inputNiifLiquidacion.setAttribute("style","float:left; width:158px;");
		//AJUSTE
		inputColgaapAjuste.setAttribute("style","float:left; width:135px;");
		inputNiifAjuste.setAttribute("style","float:left; width:158px;");


		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptos_id_cuenta_colgaap','nominaConceptos_cuenta_colgaap')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_colgaap").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick','sincronizaPucPagoNiif(\'btn_sincronizar_niif\',\'nominaConceptos_id_cuenta_niif\',\'nominaConceptos_cuenta_niif\')');
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png"/>';
		document.getElementById('DIV_nominaConceptos_cuenta_colgaap').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptos_id_cuenta_niif','nominaConceptos_cuenta_niif')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_niif").appendChild(divBtnPlantilla);

		//========================BOTONES PARA LAS CUENTAS DE CONTRAPARTIDA =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptos_id_cuenta_contrapartida_colgaap','nominaConceptos_cuenta_contrapartida_colgaap')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_contrapartida_colgaap").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_contrapartida');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick','sincronizaPucPagoNiif(\'btn_sincronizar_niif_contrapartida\',\'nominaConceptos_id_cuenta_contrapartida_niif\',\'nominaConceptos_cuenta_contrapartida_niif\')');
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptos_cuenta_contrapartida_colgaap').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptos_id_cuenta_contrapartida_niif','nominaConceptos_cuenta_contrapartida_niif')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_contrapartida_niif").appendChild(divBtnPlantilla);


		//======================== BOTONES PARA LAS CUENTAS DE LIQUIDACION =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptos_id_cuenta_colgaap_liquidacion','nominaConceptos_cuenta_colgaap_liquidacion')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_colgaap_liquidacion").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_liquidacion');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick',"sincronizaPucPagoNiif('btn_sincronizar_niif_liquidacion','nominaConceptos_id_cuenta_niif_liquidacion','nominaConceptos_cuenta_niif_liquidacion')");
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptos_cuenta_colgaap_liquidacion').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptos_id_cuenta_niif_liquidacion','nominaConceptos_cuenta_niif_liquidacion')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_niif_liquidacion").appendChild(divBtnPlantilla);

		//======================== BOTONES PARA LAS CUENTAS DE AJUSTE =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptos_id_cuenta_colgaap_ajuste','nominaConceptos_cuenta_colgaap_ajuste')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_colgaap_ajuste").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_ajuste');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick',"sincronizaPucPagoNiif('btn_sincronizar_niif_ajuste','nominaConceptos_id_cuenta_niif_ajuste','nominaConceptos_cuenta_niif_ajuste')");
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptos_cuenta_colgaap_ajuste').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptos_id_cuenta_niif_ajuste','nominaConceptos_cuenta_niif_ajuste')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptos_cuenta_niif_ajuste").appendChild(divBtnPlantilla);

		// VALIDAR QUE LAS CUENTAS EXISTAN EN EL PUC DE CADA LIBRO
		var arrayDatos = {};
		arrayDatos[0] = {};
		arrayDatos[1] = {};
		arrayDatos[2] = {};
		arrayDatos[3] = {};
		arrayDatos[4] = {};
		arrayDatos[5] = {};
		arrayDatos[6] = {};
		arrayDatos[7] = {};
		if (document.getElementById('nominaConceptos_id_cuenta_colgaap')){
			arrayDatos[0].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_colgaap').value;
			arrayDatos[0].field     = "nominaConceptos_cuenta_colgaap";
			arrayDatos[0].puc       = "puc";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_niif')){
			arrayDatos[1].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_niif').value;
			arrayDatos[1].field     = "nominaConceptos_cuenta_niif";
			arrayDatos[1].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_contrapartida_colgaap')){
			arrayDatos[2].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_contrapartida_colgaap').value;
			arrayDatos[2].field     = "nominaConceptos_cuenta_contrapartida_colgaap";
			arrayDatos[2].puc       = "puc";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_contrapartida_niif')){
			arrayDatos[3].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_contrapartida_niif').value;
			arrayDatos[3].field     = "nominaConceptos_cuenta_contrapartida_niif";
			arrayDatos[3].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_colgaap_liquidacion') && document.getElementById('nominaConceptos_naturaleza').value=='Provision'){
			arrayDatos[4].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_colgaap_liquidacion').value;
			arrayDatos[4].field     = "nominaConceptos_cuenta_colgaap_liquidacion";
			arrayDatos[4].puc       = "puc";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_niif_liquidacion') && document.getElementById('nominaConceptos_naturaleza').value=='Provision'){
			arrayDatos[5].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_niif_liquidacion').value;
			arrayDatos[5].field     = "nominaConceptos_cuenta_niif_liquidacion";
			arrayDatos[5].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_colgaap_ajuste') && document.getElementById('nominaConceptos_concepto_ajustable').value=='true'){
			arrayDatos[6].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_colgaap_ajuste').value;
			arrayDatos[6].field     = "nominaConceptos_cuenta_colgaap_ajuste";
			arrayDatos[6].puc       = "puc";
		}
		if (document.getElementById('nominaConceptos_id_cuenta_niif_ajuste') && document.getElementById('nominaConceptos_concepto_ajustable').value=='true'){
			arrayDatos[7].id_cuenta = document.getElementById('nominaConceptos_id_cuenta_niif_ajuste').value;
			arrayDatos[7].field     = "nominaConceptos_cuenta_niif_ajuste";
			arrayDatos[7].puc       = "puc_niif";
		}

		Ext.Ajax.request({
			    url     : 'nomina_conceptos/bd/bd.php',
			    params  :
			    {
					opc          : 'validaCuentas',
					json_cuentas : JSON.stringify(arrayDatos)
			    },
			    success :function (result, request){
		    				var resul = JSON.parse(result.responseText);
		    				resul.forEach(function(element) {
		    					let cuenta_campo = (document.getElementById(element.field))? document.getElementById(element.field).value : element.cuenta ;
					  			if (element.cuenta==false || element.cuenta!=cuenta_campo){
					  				if (document.getElementById(element.field)){
					  					document.getElementById(element.field).value = '';
					  					if (element.id_cuenta>0) {
					  						document.getElementById(element.field).placeholder = 'Cuenta Invalida';
					  						document.getElementById(element.field).style.backgroundColor = '#fcbb1aad';
					  					}
					  				}
					  			}
							});
			            },
			    failure : function(){
			    	MyLoading2('off',{icono:'fail',texto:'No se cargaron los formatos<br>intentelo de nuevo',duracion:3000 });
			    }
		});

		function sincronizaPucPagoNiif(divLoad,campoId,campoText){
			var cuenta='';
			if (divLoad=='btn_sincronizar_niif_ajuste'){
				cuenta=inputColgaapAjuste.value;
			}
			else if (divLoad=='btn_sincronizar_niif_liquidacion'){
				cuenta=inputColgaapLiquidacion.value;
			}
			else if (divLoad=='btn_sincronizar_niif_contrapartida') {
				cuenta=inputContrapartidaColgaap.value ;
			}
			else{
				cuenta=inputColgaap.value
			}

			if(isNaN(cuenta) || cuenta == 0){ alert("Aviso\nSeleccione una cuenta para sincronizar"); return; }

			Ext.get(divLoad).load({
				url     : 'nomina_conceptos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc       : 'sincronizaPucPagoNiif',
					cuenta    : cuenta,
					campoId   : campoId,
					campoText : campoText,
				}
			});
		}

		//VALIDAR EL CARACTER DE LAS CUENTAS
		document.getElementById('nominaConceptos_caracter').setAttribute("onchange","validaCaracterCuentas('nominaConceptos_caracter')");
		document.getElementById('nominaConceptos_caracter_contrapartida').setAttribute("onchange","validaCaracterCuentas('nominaConceptos_caracter_contrapartida')");

		function validaCaracterCuentas(campoId) {
			var caracter = document.getElementById('nominaConceptos_caracter').value;
			var caracter_contrapartida = document.getElementById('nominaConceptos_caracter_contrapartida').value;

			if (caracter==caracter_contrapartida && caracter!=0 && caracter_contrapartida!=0) {
				alert("El caracter de las cuentas debe ser debito y credito, no pueden ser iguales");
				document.getElementById(campoId).value='';
			}

		}

		//VALIDAR EL CARACTER DE LAS CUENTAS
		document.getElementById('nominaConceptos_centro_costos').setAttribute("onchange","validaCentroCostos('nominaConceptos_centro_costos')");
		document.getElementById('nominaConceptos_centro_costos_contrapartida').setAttribute("onchange","validaCentroCostos('nominaConceptos_centro_costos_contrapartida')");

		// VALIDAR QUE SOLO TENGA UN CENTRO DE COSTOS PARA UNA CUENTA
		function validaCentroCostos(campoId) {
			var caracter = document.getElementById('nominaConceptos_centro_costos').value;
			var caracter_contrapartida = document.getElementById('nominaConceptos_centro_costos_contrapartida').value;
			// console.log(caracter);
			if (caracter=='false' && caracter_contrapartida=='false') {return;}
			if (caracter==caracter_contrapartida && caracter!=0 && caracter_contrapartida!=0) {
				alert("Solo se puede un centro de costos");
				document.getElementById(campoId).value='';
			}
		}

		// =================== FUNCION PARA LOS CONCEPTOS ========================== //
		var inputFuncion=document.getElementById('nominaConceptos_formula');
		inputFuncion.readOnly=true;
		inputFuncion.setAttribute('onclick','ventanaFormulaConcepto()');

		function ventanaFormulaConcepto() {

			Win_Ventana_formula_concepto = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_formula_concepto',
			    title       : 'Crear formula para el calculo del concepto',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc     : 'ventanaFormulaConcepto',
						formula : inputFuncion.value,
						opcion  : '<?php echo $opcion ?>',
						id      : '<?php echo $id ?>',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            // style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Aceptar',
			                    scale       : 'large',
			                    iconCls     : 'ok',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); resultadoVentanaFormula();}
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
			                    handler     : function(){ BloqBtn(this); Win_Ventana_formula_concepto.close(id) }
			                },
			            ]
			        },
			        {
						xtype	: 'buttongroup',
						columns	: 1,
						title	: 'Niveles de la Formula',
						items	:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 160,
								height		: 56,
								bodyStyle 	: 'background-color:rgba(255,255,255,0)',
								autoLoad    :
								{
									url		: 'nomina_conceptos/bd/bd.php',
									scripts	: true,
									nocache	: true,
									params	:
									{
										opc    : "niveles_formula",
										opcion : '<?php echo $opcion ?>',
										id     : '<?php echo $id ?>',
									}
								}
							}
						]
					}
			    ]
			}).show();
		}

		var inputFuncion2=document.getElementById('nominaConceptos_formula_liquidacion');
		inputFuncion2.readOnly=true;
		inputFuncion2.setAttribute('onclick','ventanaFormulaConceptoLiquidacion()');

		function ventanaFormulaConceptoLiquidacion() {

			Win_Ventana_formula_concepto = new Ext.Window({
			    width       : 500,
			    height      : 500,
			    id          : 'Win_Ventana_formula_concepto',
			    title       : 'Crear formula para el calculo del concepto',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'nomina_conceptos/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						opc     : 'ventanaFormulaConceptoLiquidacion',
						formula : inputFuncion2.value,
						opcion  : '<?php echo $opcion ?>',
						id      : '<?php echo $id ?>',
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            // style   : 'border-right:none;',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Aceptar',
			                    scale       : 'large',
			                    iconCls     : 'ok',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); resultadoVentanaFormula('liquidacion');}
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
			                    handler     : function(){ BloqBtn(this); Win_Ventana_formula_concepto.close(id) }
			                },
			            ]
			        },
			        {
						xtype	: 'buttongroup',
						columns	: 1,
						title	: 'Niveles de la Formula',
						items	:
						[
							{
								xtype		: 'panel',
								border		: false,
								width		: 160,
								height		: 56,
								bodyStyle 	: 'background-color:rgba(255,255,255,0)',
								autoLoad    :
								{
									url		: 'nomina_conceptos/bd/bd.php',
									scripts	: true,
									nocache	: true,
									params	:
									{
										opc    : "niveles_formula_liquidacion",
										opcion : '<?php echo $opcion ?>',
										id     : '<?php echo $id ?>',
									}
								}
							}
						]
					}
			    ]
			}).show();
		}

		function resultadoVentanaFormula(opc) {
			if (opc=='liquidacion') {
				document.getElementById('nominaConceptos_formula_liquidacion').value = document.getElementById('formula_concepto').value;
				document.getElementById('nominaConceptos_nivel_formula_liquidacion').value = document.getElementById('nivel_formula_liquidacion').value;
				Win_Ventana_formula_concepto.close(id)
			}
			else{
				document.getElementById('nominaConceptos_formula').value       = document.getElementById('formula_concepto').value;
				document.getElementById('nominaConceptos_nivel_formula').value = document.getElementById('nivel_formula').value;
				Win_Ventana_formula_concepto.close(id)
			}
		}

		//

		//ASIGNAR EL EVENTO DEL TIPO DE CONCEPTO PARA MOSTRAR U OCULTAR LA CUENTA DE LIQUIDACION
		document.getElementById('nominaConceptos_naturaleza').setAttribute("onchange","cambiaNaturalezaConcepto()");
		cambiaNaturalezaConcepto();
		function cambiaNaturalezaConcepto() {
			var naturaleza         = document.getElementById('nominaConceptos_naturaleza').value;
			var concepto_ajustable = document.getElementById('nominaConceptos_concepto_ajustable').value;

			if (naturaleza=='Provision' ) {
				document.querySelectorAll('.EmpSeparador')[4].style.display = 'block';
				document.getElementById('EmpConte_nominaConceptos_cuenta_colgaap_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_cuenta_niif_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_tercero_cruce_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_formula_liquidacion').setAttribute('style','display:block;');

				if ('<?php echo $opcion; ?>'!='Vupdate') {
					document.getElementById('nominaConceptos_cuenta_colgaap_liquidacion').value    = '';
					document.getElementById('nominaConceptos_id_cuenta_colgaap_liquidacion').value = '';
					document.getElementById('nominaConceptos_cuenta_niif_liquidacion').value       = '';
					document.getElementById('nominaConceptos_id_cuenta_niif_liquidacion').value    = '';
					document.getElementById('nominaConceptos_tercero_cruce_liquidacion').value     = '';
					document.getElementById('nominaConceptos_formula_liquidacion').value           = '';
				}


			}
			else{

				document.querySelectorAll('.EmpSeparador')[4].style.display = 'none';
				document.getElementById('EmpConte_nominaConceptos_cuenta_colgaap_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_cuenta_niif_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_tercero_cruce_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_formula_liquidacion').setAttribute('style','display:none;');

				document.getElementById('nominaConceptos_cuenta_colgaap_liquidacion').value    = 0;
				document.getElementById('nominaConceptos_id_cuenta_colgaap_liquidacion').value = 0;
				document.getElementById('nominaConceptos_cuenta_niif_liquidacion').value       = 0;
				document.getElementById('nominaConceptos_id_cuenta_niif_liquidacion').value    = 0;
				document.getElementById('nominaConceptos_tercero_cruce_liquidacion').value     = 'Empleado';
				document.getElementById('nominaConceptos_formula_liquidacion').value           = '';

			}

			cambiaCuentasConceptoAjusteado();
		}

		document.getElementById('nominaConceptos_concepto_ajustable').setAttribute("onchange","cambiaCuentasConceptoAjusteado()");
		function cambiaCuentasConceptoAjusteado() {
			var naturaleza         = document.getElementById('nominaConceptos_naturaleza').value;
			var concepto_ajustable = document.getElementById('nominaConceptos_concepto_ajustable').value;
			if (naturaleza=='Deduccion' && concepto_ajustable=='true') {
				document.querySelectorAll('.EmpSeparador')[5].style.display = 'block';
				document.getElementById('EmpConte_nominaConceptos_cuenta_colgaap_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_cuenta_niif_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_tercero_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptos_centro_costos_ajuste').setAttribute('style','display:block;');
				// document.getElementById('EmpConte_nominaConceptos_concepto_ajustable').setAttribute('style','display:block;');
				// document.getElementById('EmpConte_nominaConceptos_concepto_ajustable').setAttribute('style','display:block;');
				if ('<?php echo $opcion; ?>'!='Vupdate') {
					document.getElementById('nominaConceptos_id_cuenta_colgaap_ajuste').value = '';
					document.getElementById('nominaConceptos_cuenta_colgaap_ajuste').value    = '';
					document.getElementById('nominaConceptos_id_cuenta_niif_ajuste').value    = '';
					document.getElementById('nominaConceptos_cuenta_niif_ajuste').value       = '';
					document.getElementById('nominaConceptos_tercero_ajuste').value           = 'Empleado';
					document.getElementById('nominaConceptos_centro_costos_ajuste').value     = 'true';
					// document.getElementById('nominaConceptos_concepto_ajustable').value       = '';
				}

			}
			else {
				document.querySelectorAll('.EmpSeparador')[5].style.display = 'none';
				document.getElementById('EmpConte_nominaConceptos_cuenta_colgaap_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_cuenta_niif_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_tercero_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptos_centro_costos_ajuste').setAttribute('style','display:none;');
				// document.getElementById('EmpConte_nominaConceptos_concepto_ajustable').setAttribute('style','display:none;');

				document.getElementById('nominaConceptos_id_cuenta_colgaap_ajuste').value = 0;
				document.getElementById('nominaConceptos_cuenta_colgaap_ajuste').value    = 0;
				document.getElementById('nominaConceptos_id_cuenta_niif_ajuste').value    = 0;
				document.getElementById('nominaConceptos_cuenta_niif_ajuste').value       = 0;
				document.getElementById('nominaConceptos_tercero_ajuste').value           = 'Empleado';
				document.getElementById('nominaConceptos_centro_costos_ajuste').value     = 'true';

				// document.getElementById('nominaConceptos_concepto_ajustable').value='false';
			}
		}

		// ACTUALIZAR LA OPCION SI EL CAMPO DE TEXTO LE RESTA A LOS DIAS LABORADOS
		function updateRestarCt(valor) {
			Ext.get('divLoadRestarCt').load({
				url     : 'nomina_conceptos/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'updateRestarCt',
					restar : valor,
				}
			});
		}

	</script>

<?php
}
?>