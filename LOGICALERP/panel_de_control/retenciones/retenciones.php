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

	$id_pais    = $_SESSION['PAIS'];
	$id_empresa = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'retencion';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'retenciones';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA
		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 630;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 225;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'retencion,valor,modulo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Id','id',50);
			$grilla->AddRow('Retencion','retencion',300);
			$grilla->AddRow('%','valor',50);
			$grilla->AddRow('Modulo','modulo',50);
			$grilla->AddRow('Base','base',100);

			$grilla->AddColStyle('valor','text-align:right; width:45px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
			$grilla->AddColStyle('base','text-align:right; width:95px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 310;
			$grilla->FColumnaGeneralAncho	= 310;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Retencion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva retencion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 350;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 510;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Descripci&oacute;n');
			$grilla->AddTextField('Nombre','retencion',150,'true','false');
			$grilla->AddTextField('% Valor','valor',150,'true','false');
			$grilla->AddTextField('Base','base',150,'true','false');
			$grilla->AddComboBox('Modulo','modulo',150,'true','false','Compra:Compra,Venta:Venta');
			$grilla->AddComboBox('Tipo Retencion','tipo_retencion',150,'true','false','ReteFuente:Retencion en la fuente,ReteIva:Retencion Iva,ReteIca:Retencion ICA,AutoRetencion:AutoRetencion');
			$grilla->AddComboBox('Carga Automatica','factura_auto',150,'true','false','true:Si,false:No');
			$grilla->AddComboBox('Departamento','id_departamento',150,'false','true','ubicacion_departamento,id,departamento,true','activo=1 AND id_pais='.$id_pais);
			$grilla->AddComboBox('Ciudad','id_ciudad',150,'false','true','0:Problema al Cargar la base de datos');

			$grilla->AddSeparator('Contabilidad');
			$grilla->AddTextField('Cuenta Colgaap','cuenta',150,'true','false');
			$grilla->AddTextField('Cuenta Niif','cuenta_niif',150,'true','false');
			$grilla->AddTextField('Cuenta AutoRetencion','cuenta_autoretencion',150,'true','false');
			$grilla->AddTextField('Cuenta AutoRetencion Niif','cuenta_autoretencion_niif',150,'true','false');

			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);
			$grilla->AddTextField('','grupo_empresarial',150,'true','true',$grupo_empresa);

		//VALIDACIONES
			$grilla->AddValidation('retencion','mayuscula');
			$grilla->AddValidation('valor','numero-real');
			$grilla->AddValidation('base','numero-real');
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
		var tipo_retencion, cuenta, cuenta_niif, cuenta_autoretencion, cuenta_autoretencion_niif, id_departamento, id_ciudad, modulo;

		function ventanaBuscarCuentaRetencion(idInput){

			var typeCuenta = (idInput.search('niif') > 0)? 'niif': ''
			,	title = (typeCuenta == '')? 'Seleccione la cuenta Colgaap': 'Seleccione la cuenta Niif';

			if(tipo_retencion.value == ''){ alert('Aviso,\nSeleccione primero el tipo de retencion.'); return; }


			Win_VentanaBuscarPucRetenciones = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucRetenciones',
	            title       : title,
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'retenciones/busqueda_puc_retenciones.php',
	                scripts : true,
	                nocache : true,
	                params  : { typeCuenta : typeCuenta, idInput : idInput }
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarPucRetenciones.close(); }
	                }
	            ]
	        }).show();
		}

		function updateTypeRetencion(input){
			if(input.value == 'AutoRetencion'){
				cuenta_autoretencion.value      = '';
				cuenta_autoretencion_niif.value = '';
				document.getElementById('EmpConte_retencion_cuenta_autoretencion').style.display      ='block';
				document.getElementById('EmpConte_retencion_cuenta_autoretencion_niif').style.display ='block';
			}
			else{
				cuenta_autoretencion.value      = 0;
				cuenta_autoretencion_niif.value = 0;
				document.getElementById('EmpConte_retencion_cuenta_autoretencion').style.display      ='none';
				document.getElementById('EmpConte_retencion_cuenta_autoretencion_niif').style.display ='none';
			}
		}

		function agregarBtnBuscarCuentaRetenciones(input){
			input.readOnly = true;
			input.setAttribute("style","float:left; width:108px;");

			var idInput = input.id;

			var btnBuscarCuenta = document.createElement('div');
			btnBuscarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnBuscarCuenta.setAttribute('title','Buscar cuenta');
			btnBuscarCuenta.setAttribute('onclick','ventanaBuscarCuentaRetencion("'+idInput+'")');
			btnBuscarCuenta.innerHTML = '<img src="img/buscar20.png" />';
			document.getElementById('DIV_'+idInput).appendChild(btnBuscarCuenta);
		}

		function agregarBtnSincronizarCuentaRetenciones(input){
			var idInput = input.id
			,	estado  = idInput.split('_')[1];

			var btnSincronizarCuenta = document.createElement('div');
			btnSincronizarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnSincronizarCuenta.setAttribute('id','btn_sincronizar_'+idInput);
			btnSincronizarCuenta.setAttribute('title','Sincronizar cuenta en Niif');
			btnSincronizarCuenta.innerHTML = '<img src="img/refresh.png" onclick="sincronizaCuentaRetencionEnNiif(\''+idInput+'\')"/>';
			document.getElementById('DIV_'+idInput).appendChild(btnSincronizarCuenta);
		}

		function sincronizaCuentaRetencionEnNiif(idInput){

			var cuenta = document.getElementById(idInput).value;
			if(isNaN(cuenta) || cuenta == 0){ alert("Aviso\nSeleccione una cuenta para sincronizar"); return; }

			Ext.get('btn_sincronizar_'+idInput).load({
				url     : 'retenciones/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc      : 'sincronizaPucImpuestoNiif',
					idInput : idInput,
					cuenta  : cuenta
				}
			});
		}


	</script>
<?php }

else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>

	<script>
		tipo_retencion = document.getElementById('retencion_tipo_retencion');
		tipo_retencion.setAttribute("onchange","updateTypeRetencion(this)");

		modulo = document.getElementById('retencion_modulo');
		// modulo.setAttribute("onchange","updateComboRetencion(this)");

		cuenta = document.getElementById('retencion_cuenta');
		cuenta_niif  = document.getElementById('retencion_cuenta_niif');

		cuenta_autoretencion      = document.getElementById('retencion_cuenta_autoretencion');
		cuenta_autoretencion_niif = document.getElementById('retencion_cuenta_autoretencion_niif');

		agregarBtnBuscarCuentaRetenciones(cuenta);
		agregarBtnBuscarCuentaRetenciones(cuenta_niif);
		agregarBtnBuscarCuentaRetenciones(cuenta_autoretencion);
		agregarBtnBuscarCuentaRetenciones(cuenta_autoretencion_niif);

		agregarBtnSincronizarCuentaRetenciones(cuenta);
		agregarBtnSincronizarCuentaRetenciones(cuenta_autoretencion);


		//======================= CUENTA NIIF =======================//

		if ('<?php echo $opcion; ?>'=='Vupdate') {
			if (tipo_retencion.value!='AutoRetencion') {
				setTimeout(function(){
					cuenta_autoretencion.value = 0;
					cuenta_autoretencion_niif.value = 0;
					document.getElementById('EmpConte_retencion_cuenta_autoretencion').style.display      ='none';
					document.getElementById('EmpConte_retencion_cuenta_autoretencion_niif').style.display ='none';
				}, 70);
			}
		}

		var arraySeparador = document.getElementById('Formularioretencion').querySelectorAll('.EmpSeparador');
		for(i in arraySeparador){
			if(!isNaN(i) && i > 0){ arraySeparador[i].setAttribute('style','margin-top:10px'); }
		}

		//==================== UPDATE COMBO CIUDAD ====================//
		var comboDepartamento = Ext.get('retencion_id_departamento');
		comboDepartamento.addListener(
			'change',
			function(event,element,options){
				id_departamento = document.getElementById('retencion_id_departamento').value;
				ActualizaComboCiudad(id_departamento);
			},this
		);

		ActualizaComboCiudad(document.getElementById('retencion_id_departamento').value);

		function ActualizaComboCiudad(id_departamento){
			var MyParent = document.getElementById('retencion_id_ciudad').parentNode;
			Ext.get(MyParent).load({
				url		: 'retenciones/bd/bd.php',
				timeout : 180000,
				scripts	: true,
				nocache	: true,
				params	:
				{
					opc				: 'optionCiudad',
					id_retencion	: '<?php echo $id?>',
					id_departamento	: id_departamento
				}
			});
		};


	</script>
<?php
} ?>