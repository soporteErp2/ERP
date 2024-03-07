<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");


	$id_empresa = $_SESSION['EMPRESA'];

	// SI ES UNA PROVISION CONSULTAR SU CUENTA DE LIQUIDACION
	if ($naturaleza=='Provision') {
		$sql="SELECT id_cuenta_colgaap_liquidacion,cuenta_colgaap_liquidacion,id_cuenta_niif_liquidacion,cuenta_niif_liquidacion,tercero_cruce_liquidacion,nivel_formula_liquidacion,formula_liquidacion
				FROM nomina_conceptos WHERE activo=1 AND id_empresa=$id_empresa AND id=$id_concepto";
		$query=mysql_query($sql,$link);
		$id_cuenta_colgaap_liquidacion = mysql_result($query,0,'id_cuenta_colgaap_liquidacion');
		$cuenta_colgaap_liquidacion    = mysql_result($query,0,'cuenta_colgaap_liquidacion');
		$id_cuenta_niif_liquidacion    = mysql_result($query,0,'id_cuenta_niif_liquidacion');
		$cuenta_niif_liquidacion       = mysql_result($query,0,'cuenta_niif_liquidacion');
		$tercero_cruce_liquidacion     = mysql_result($query,0,'tercero_cruce_liquidacion');
		$nivel_formula_liquidacion     = mysql_result($query,0,'nivel_formula_liquidacion');
		$formula_liquidacion           = mysql_result($query,0,'formula_liquidacion');
	}


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'nominaConceptosGruposTrabajo';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'nomina_conceptos_grupos_trabajo';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_concepto=$id_concepto ";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			$grilla->CamposBusqueda		= 'grupo_trabajo,caracter,caracter_contrapartida,cuenta_colgaap,cuenta_niif,cuenta_contrapartida_colgaap,cuenta_contrapartida_niif';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Grupo de Trabajo','grupo_trabajo',150);
			// $grilla->AddValidation('codigo','numero');
			// $grilla->AddValidation('codigo','unico_global',' id_empresa='.$id_empresa);
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
			$grilla->VBotonNText		= 'Agregar G. de Trabajo'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'reunionadd';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_configuracion_grupos_trabajo.close();Actualiza_Div_nominaConceptos('.$id_concepto.');');
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
			$grilla->AddTextField('','concepto',180,'true','true',$concepto);
			$grilla->AddTextField('','id_concepto',180,'true','true',$id_concepto);
			$grilla->AddTextField('Grupo de Trabajo:','grupo_trabajo',180,'true','false');
			$grilla->AddTextField('','id_grupo_trabajo',180,'true','true');
			$grilla->AddTextField('','id_empresa',180,'true','true',$id_empresa);


			// $grilla->AddTextField('Tercero Cruce:','tercero',180,'true','false');
			// $grilla->AddTextField('','id_tercero',180,'false','true');

			$grilla->AddSeparator('Configuracion Cuentas');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap',180,'true');
			$grilla->AddTextField('','id_cuenta_colgaap',180,'true','true');
			$grilla->AddTextField('Cuenta NIIF:','cuenta_niif',180,'true');
			$grilla->AddTextField('','id_cuenta_niif',180,'true','true');
			$grilla->AddComboBox('Caracter:','caracter',180,'true','false','debito:Debito,credito:Credito');
			$grilla->AddComboBox('Centro de Costos:','centro_costos',180,'true','false','false:No,true:Si');

			$grilla->AddSeparator('Configuracion Cuentas Contrapartida');
			$grilla->AddTextField('Cuenta Cruce Colgaap:','cuenta_contrapartida_colgaap',180,'true');
			$grilla->AddTextField('','id_cuenta_contrapartida_colgaap',180,'true','true');
			$grilla->AddTextField('Cuenta Cruce NIIF:','cuenta_contrapartida_niif',180,'true');
			$grilla->AddTextField('','id_cuenta_contrapartida_niif',180,'true','true');
			$grilla->AddComboBox('Caracter:','caracter_contrapartida',180,'true','false','debito:Debito,credito:Credito');
			$grilla->AddComboBox('Centro de Costos:','centro_costos_contrapartida',180,'true','false','false:No,true:Si');

			$grilla->AddSeparator('Cuenta Liquidacion');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap_liquidacion',180,'true');
			$grilla->AddTextField('','id_cuenta_colgaap_liquidacion',180,'true','true');
			$grilla->AddTextField('Cruce NIIF:','cuenta_niif_liquidacion',180,'true');
			$grilla->AddTextField('','id_cuenta_niif_liquidacion',180,'true','true');
			$grilla->AddComboBox('Tercero:','tercero_cruce_liquidacion',180,'true','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddTextField('Formula de liquidacion:','formula_liquidacion',180,'false');
			$grilla->AddTextField('','nivel_formula_liquidacion',180,'false','true');

			$grilla->AddSeparator('Cuenta Ajuste');
			$grilla->AddTextField('Cuenta Colgaap:','cuenta_colgaap_ajuste',180,'true');
			$grilla->AddTextField('','id_cuenta_colgaap_ajuste',180,'true','true');
			$grilla->AddTextField('Cuenta NIIF:','cuenta_niif_ajuste',180,'true');
			$grilla->AddTextField('','id_cuenta_niif_ajuste',180,'true','true');
			$grilla->AddComboBox('Tercero:','tercero_ajuste',180,'true','false','Empleado:Empleado,Entidad:Entidad');
			$grilla->AddComboBox('Centro de Costos:','centro_costos_ajuste',180,'true','false','false:No,true:Si');

			$grilla->AddSeparator('Configuracion Formula');
			$grilla->AddTextField('Base:','base',180,'false');
			$grilla->AddTextField('Formula:','formula',180,'false');
			$grilla->AddTextField('','nivel_formula',180,'false','true');



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

		//function Agregar_nominaConceptosGruposTrabajo(){ ventana_concepto_nomina(0); }
		//function Editar_nominaConceptosGruposTrabajo(id){ ventana_concepto_nomina(id); }

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
	                    handler     : function(){ btnnominaConceptosGruposTrabajo(id); }
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
	                    handler     : function(){ eliminarnominaConceptosGruposTrabajo(id); }
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

		function btnnominaConceptosGruposTrabajo(id){
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

		function eliminarnominaConceptosGruposTrabajo(id) {
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

			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_VentanaBuscarPucCuentasPago = new Ext.Window({
	            width       : myancho-100,
	            height      : myalto-50,
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
			// 	document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap').value=id;
			// 	document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap').value=cuenta;
			// }
			// else{
			// 	document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif').value=id;
			// 	document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif').value=cuenta;
			// }
			Win_VentanaBuscarPucCuentasPago.close();
		}

		function ventanaBuscarGrupoTrabajo() {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_VentanaBuscarGrupoTrabajo = new Ext.Window({
	            width       : 450,
	            height      : 400,
	            id          : 'Win_VentanaBuscarGrupoTrabajo',
	            title       : 'Seleccionar Grupo de trabajo',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'nomina_conceptos/bd/buscar_grupo_trabajo.php',
	                scripts : true,
	                nocache : true,
	                params  :
	                		{
								id_concepto : '<?php echo $id_concepto; ?>',
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
						handler   : function(){ Win_VentanaBuscarGrupoTrabajo.close(); }
	                }
	            ]
	        }).show();
		}



	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>

		if ('<?php echo $opcion ?>'=='Vagregar') {
			document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap').value               = '<?php echo $id_cuenta_colgaap; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap').value                  = '<?php echo $cuenta_colgaap; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif').value                  = '<?php echo $id_cuenta_niif; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif').value                     = '<?php echo $cuenta_niif; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_colgaap').value = '<?php echo $id_cuenta_contrapartida_colgaap; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap').value    = '<?php echo $cuenta_contrapartida_colgaap; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_niif').value    = '<?php echo $id_cuenta_contrapartida_niif; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_cuenta_contrapartida_niif').value       = '<?php echo $cuenta_contrapartida_niif; ?>';

			document.getElementById('nominaConceptosGruposTrabajo_caracter').value                        = '<?php echo $caracter; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_caracter_contrapartida').value          = '<?php echo $caracter_contrapartida; ?>';

			document.getElementById('nominaConceptosGruposTrabajo_centro_costos').value                   = '<?php echo $centro_costos; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_centro_costos_contrapartida').value     = '<?php echo $centro_costos_contrapartida; ?>';

			document.getElementById('nominaConceptosGruposTrabajo_nivel_formula').value                   = '<?php echo $nivel_formula; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_formula').value                         = '<?php echo $formula_concepto; ?>';

			document.getElementById('nominaConceptosGruposTrabajo_nivel_formula_liquidacion').value       = '<?php echo $nivel_formula_liquidacion; ?>';
			document.getElementById('nominaConceptosGruposTrabajo_formula_liquidacion').value             = '<?php echo $formula_liquidacion; ?>';

		}

		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES
		var inputGrupoTrabajo = document.getElementById('nominaConceptosGruposTrabajo_grupo_trabajo');
		var inputColgaap      = document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap');
		var inputNiif         = document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES CONTRAPARTIDA
		var inputContrapartidaColgaap = document.getElementById('nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap');
		var inputContrapartidaNiif    = document.getElementById('nominaConceptosGruposTrabajo_cuenta_contrapartida_niif');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES PARA LA LIQUIDACION
		var inputColgaapLiquidacion   = document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion');
		var inputNiifLiquidacion      = document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_liquidacion');
		//AGREGAR LAS FUNCIONES PARA BUSCAR LAS CUENTAS CONTABLES PARA EL AJUSTE
		var inputColgaapAjuste   = document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste');
		var inputNiifAjuste      = document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_ajuste');

		inputGrupoTrabajo.readOnly         = true;
		inputColgaap.readOnly              = true;
		inputNiif.readOnly                 = true;
		//CONTRAPARTIDA
		inputContrapartidaColgaap.readOnly = true;
		inputContrapartidaNiif.readOnly    = true;
		//LIQUIDACION
		inputColgaapLiquidacion.readOnly   = true;
		inputNiifLiquidacion.readOnly      = true;
		//AJUSTE
		inputColgaapAjuste.readOnly        = true;
		inputNiifAjuste.readOnly           = true;

		inputGrupoTrabajo.setAttribute("style","float:left; width:158px;");
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


		var divBtnGrupoTrabajo = document.createElement("div");
		divBtnGrupoTrabajo.setAttribute("class","divBtnBuscarPuc");
		divBtnGrupoTrabajo.setAttribute("onclick","ventanaBuscarGrupoTrabajo()");
		divBtnGrupoTrabajo.setAttribute('title','Buscar Grupo de trabajo');
		divBtnGrupoTrabajo.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_grupo_trabajo").appendChild(divBtnGrupoTrabajo);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptosGruposTrabajo_id_cuenta_colgaap','nominaConceptosGruposTrabajo_cuenta_colgaap')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_colgaap").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick','sincronizaPucPagoNiif(\'btn_sincronizar_niif\',\'nominaConceptosGruposTrabajo_id_cuenta_niif\',\'nominaConceptosGruposTrabajo_cuenta_niif\')');
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png"/>';
		document.getElementById('DIV_nominaConceptosGruposTrabajo_cuenta_colgaap').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptosGruposTrabajo_id_cuenta_niif','nominaConceptosGruposTrabajo_cuenta_niif')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_niif").appendChild(divBtnPlantilla);

		//========================BOTONES PARA LAS CUENTAS DE CONTRAPARTIDA =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptosGruposTrabajo_id_cuenta_contrapartida_colgaap','nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_contrapartida');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick','sincronizaPucPagoNiif(\'btn_sincronizar_niif_contrapartida\',\'nominaConceptosGruposTrabajo_id_cuenta_contrapartida_niif\',\'nominaConceptosGruposTrabajo_cuenta_contrapartida_niif\')');
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptosGruposTrabajo_id_cuenta_contrapartida_niif','nominaConceptosGruposTrabajo_cuenta_contrapartida_niif')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_contrapartida_niif").appendChild(divBtnPlantilla);

		//======================== BOTONES PARA LAS CUENTAS DE LIQUIDACION =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptosGruposTrabajo_id_cuenta_colgaap_liquidacion','nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_liquidacion');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick',"sincronizaPucPagoNiif('btn_sincronizar_niif_liquidacion','nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion','nominaConceptosGruposTrabajo_cuenta_niif_liquidacion')");
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion','nominaConceptosGruposTrabajo_cuenta_niif_liquidacion')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_niif_liquidacion").appendChild(divBtnPlantilla);

		//======================== BOTONES PARA LAS CUENTAS DE AJUSTE =====================================//
		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','nominaConceptosGruposTrabajo_id_cuenta_colgaap_ajuste','nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_ajuste');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.setAttribute('onclick',"sincronizaPucPagoNiif('btn_sincronizar_niif_ajuste','nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste','nominaConceptosGruposTrabajo_cuenta_niif_ajuste')");
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" />';
		document.getElementById('DIV_nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif','nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste','nominaConceptosGruposTrabajo_cuenta_niif_ajuste')");
		divBtnPlantilla.setAttribute('title','Buscar Cuenta Niif');
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_nominaConceptosGruposTrabajo_cuenta_niif_ajuste").appendChild(divBtnPlantilla);

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
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap')){
			arrayDatos[0].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap').value;
			arrayDatos[0].field     = "nominaConceptosGruposTrabajo_cuenta_colgaap";
			arrayDatos[0].puc       = "puc";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif')){
			arrayDatos[1].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif').value;
			arrayDatos[1].field     = "nominaConceptosGruposTrabajo_cuenta_niif";
			arrayDatos[1].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_colgaap')){
			arrayDatos[2].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_colgaap').value;
			arrayDatos[2].field     = "nominaConceptosGruposTrabajo_cuenta_contrapartida_colgaap";
			arrayDatos[2].puc       = "puc";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_niif')){
			arrayDatos[3].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_contrapartida_niif').value;
			arrayDatos[3].field     = "nominaConceptosGruposTrabajo_cuenta_contrapartida_niif";
			arrayDatos[3].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_liquidacion') && "<?php echo $naturaleza; ?>"=='Provision'){
			arrayDatos[4].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_liquidacion').value;
			arrayDatos[4].field     = "nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion";
			arrayDatos[4].puc       = "puc";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion') && "<?php echo $naturaleza; ?>"=='Provision'){
			arrayDatos[5].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion').value;
			arrayDatos[5].field     = "nominaConceptosGruposTrabajo_cuenta_niif_liquidacion";
			arrayDatos[5].puc       = "puc_niif";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_ajuste') && "<?php echo $naturaleza; ?>"=='true'){
			arrayDatos[6].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_ajuste').value;
			arrayDatos[6].field     = "nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste";
			arrayDatos[6].puc       = "puc";
		}
		if (document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste') && "<?php echo $naturaleza; ?>"=='true'){
			arrayDatos[7].id_cuenta = document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste').value;
			arrayDatos[7].field     = "nominaConceptosGruposTrabajo_cuenta_niif_ajuste";
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

			// var cuenta =(divLoad=='btn_sincronizar_niif_contrapartida')? inputContrapartidaColgaap.value : inputColgaap.value;
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
		document.getElementById('nominaConceptosGruposTrabajo_caracter').setAttribute("onchange","validaCaracterCuentas('nominaConceptosGruposTrabajo_caracter')");
		document.getElementById('nominaConceptosGruposTrabajo_caracter_contrapartida').setAttribute("onchange","validaCaracterCuentas('nominaConceptosGruposTrabajo_caracter_contrapartida')");

		function validaCaracterCuentas(campoId) {
			var caracter = document.getElementById('nominaConceptosGruposTrabajo_caracter').value;
			var caracter_contrapartida = document.getElementById('nominaConceptosGruposTrabajo_caracter_contrapartida').value;

			if (caracter==caracter_contrapartida && caracter!=0 && caracter_contrapartida!=0) {
				alert("El caracter de las cuentas debe ser debito y credito, no pueden ser iguales");
				document.getElementById(campoId).value='';
			}
		}

		//VALIDAR EL CARACTER DE LAS CUENTAS
		document.getElementById('nominaConceptosGruposTrabajo_centro_costos').setAttribute("onchange","validaCentroCostos('nominaConceptosGruposTrabajo_centro_costos')");
		document.getElementById('nominaConceptosGruposTrabajo_centro_costos_contrapartida').setAttribute("onchange","validaCentroCostos('nominaConceptosGruposTrabajo_centro_costos_contrapartida')");

		// VALIDAR QUE SOLO TENGA UN CENTRO DE COSTOS PARA UNA CUENTA
		function validaCentroCostos(campoId) {
			var caracter = document.getElementById('nominaConceptosGruposTrabajo_centro_costos').value;
			var caracter_contrapartida = document.getElementById('nominaConceptosGruposTrabajo_centro_costos_contrapartida').value;
			// console.log(caracter);
			if (caracter=='false' && caracter_contrapartida=='false') {return;}
			if (caracter==caracter_contrapartida && caracter!=0 && caracter_contrapartida!=0) {
				alert("Solo se puede un centro de costos");
				document.getElementById(campoId).value='';
			}

		}

		// =================== FUNCION PARA LOS CONCEPTOS ========================== //
		inputFuncion=document.getElementById('nominaConceptosGruposTrabajo_formula');
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
						opcion  : 'Vupdate',
						id      : '<?php echo $id_concepto; ?>',
			        }
			    },
			    tbar        :
			    [
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
						columns	: 3,
						title	: 'Nivel de la Formula',
						html : '<input value="<?php echo $nivel_formula ?>" class="myfield" readonly style="text-align:center;width:100px;">',

					}
			    ]
			}).show();
		}

		var inputFuncion2=document.getElementById('nominaConceptosGruposTrabajo_formula_liquidacion');
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
			            style   : 'border-right:none;',
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
						columns	: 3,
						title	: 'Nivel de la Formula',
						html : '<input value="<?php echo $nivel_formula ?>" class="myfield" readonly style="text-align:center;width:100px;">',
					}
			    ]
			}).show();
		}

		function resultadoVentanaFormula(opc) {
			if (opc=='liquidacion') {
				document.getElementById('nominaConceptosGruposTrabajo_formula_liquidacion').value       = document.getElementById('formula_concepto').value;
				document.getElementById('nominaConceptosGruposTrabajo_nivel_formula_liquidacion').value = '<?php echo $nivel_formula_liquidacion; ?>';
				Win_Ventana_formula_concepto.close(id)
			}
			else{
				document.getElementById('nominaConceptosGruposTrabajo_formula').value       = document.getElementById('formula_concepto').value;
				document.getElementById('nominaConceptosGruposTrabajo_nivel_formula').value = '<?php echo $nivel_formula ?>';
				Win_Ventana_formula_concepto.close(id)
			}
		}

		cambiaNaturalezaConcepto();
		function cambiaNaturalezaConcepto() {
			// var naturaleza = document.getElementById('nominaConceptosGruposTrabajo_naturaleza').value;

			if ('<?php echo $naturaleza; ?>'=='Provision') {
				document.querySelectorAll('.EmpSeparador')[3].style.display ='block';
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_niif_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_tercero_cruce_liquidacion').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_formula_liquidacion').setAttribute('style','display:block;');

				if ('<?php echo $opcion; ?>'!='Vupdate') {
					document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion').value    = '<?php echo $cuenta_colgaap_liquidacion; ?>';
					document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_liquidacion').value = '<?php echo $id_cuenta_colgaap_liquidacion; ?>';
					document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_liquidacion').value       = '<?php echo $cuenta_niif_liquidacion; ?>';
					document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion').value    = '<?php echo $id_cuenta_niif_liquidacion; ?>';
					document.getElementById('nominaConceptosGruposTrabajo_tercero_cruce_liquidacion').value     = '<?php echo $tercero_cruce_liquidacion; ?>';
					document.getElementById('nominaConceptosGruposTrabajo_formula_liquidacion').value           = '<?php echo $formula_liquidacion; ?>';
				}

			}
			else{
				document.querySelectorAll('.EmpSeparador')[3].style.display ='none';
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_niif_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_tercero_cruce_liquidacion').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_formula_liquidacion').setAttribute('style','display:none;');

				document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_liquidacion').value    = '0';
				document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_liquidacion').value = '0';
				document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_liquidacion').value       = '0';
				document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_liquidacion').value    = '0';
				document.getElementById('nominaConceptosGruposTrabajo_tercero_cruce_liquidacion').value     = 'Empleado';
				document.getElementById('nominaConceptosGruposTrabajo_formula_liquidacion').value           = '';

			}
			cambiaCuentasConceptoAjusteado();
		}

		function cambiaCuentasConceptoAjusteado() {

			if ('<?php echo $naturaleza; ?>'=='Deduccion' && '<?php echo $concepto_ajustable; ?>'=='true') {
				document.querySelectorAll('.EmpSeparador')[4].style.display = 'block';
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_niif_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_tercero_ajuste').setAttribute('style','display:block;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_centro_costos_ajuste').setAttribute('style','display:block;');
				// document.getElementById('EmpConte_nominaConceptosGruposTrabajo_concepto_ajustable').setAttribute('style','display:block;');
				// document.getElementById('EmpConte_nominaConceptosGruposTrabajo_concepto_ajustable').setAttribute('style','display:block;');
				if ('<?php echo $opcion; ?>'!='Vupdate') {
					document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_ajuste').value = '';
					document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste').value    = '';
					document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste').value    = '';
					document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_ajuste').value       = '';
					document.getElementById('nominaConceptosGruposTrabajo_tercero_ajuste').value           = 'Empleado';
					document.getElementById('nominaConceptosGruposTrabajo_centro_costos_ajuste').value     = 'true';
					// document.getElementById('nominaConceptosGruposTrabajo_concepto_ajustable').value       = '';
				}

			}
			else {
				document.querySelectorAll('.EmpSeparador')[4].style.display = 'none';
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_cuenta_niif_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_tercero_ajuste').setAttribute('style','display:none;');
				document.getElementById('EmpConte_nominaConceptosGruposTrabajo_centro_costos_ajuste').setAttribute('style','display:none;');
				// document.getElementById('EmpConte_nominaConceptosGruposTrabajo_concepto_ajustable').setAttribute('style','display:none;');

				document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_colgaap_ajuste').value = 0;
				document.getElementById('nominaConceptosGruposTrabajo_cuenta_colgaap_ajuste').value    = 0;
				document.getElementById('nominaConceptosGruposTrabajo_id_cuenta_niif_ajuste').value    = 0;
				document.getElementById('nominaConceptosGruposTrabajo_cuenta_niif_ajuste').value       = 0;
				document.getElementById('nominaConceptosGruposTrabajo_tercero_ajuste').value           = 'Empleado';
				document.getElementById('nominaConceptosGruposTrabajo_centro_costos_ajuste').value     = 'true';

				// document.getElementById('nominaConceptos_concepto_ajustable').value='false';
			}
		}

	</script>

<?php
}


?>