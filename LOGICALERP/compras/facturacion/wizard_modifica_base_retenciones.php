<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	     $grilla = new MyGrilla();			/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	$id_empresa    = $_SESSION['EMPRESA'];
	$grupo_empresa = $_SESSION['GRUPOEMPRESARIAL'];

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	= 'modifica_base_retencion';  //NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName	= 'compras_facturas_retenciones';			  												 				//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere	= "id_factura_compra = $id_factura_compra AND activo = 1"; 				//WHERE DE LA CONSULTA A LA TABLA "$TableName"

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 = 'false';		//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 = 450;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 = 200;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			        = 'true';							//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda				= 'retencion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda   = '' ;								//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA
		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Retencion','retencion',200);
			$grilla->AddRow('%','valor',50);
			$grilla->AddRow('Base','base',70);
			$grilla->AddColStyle('valor','text-align:right; width:45px !important; margin-right:5px;');   //ALINEAR NUMEROS LA DERECHA
		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 340;
			$grilla->FColumnaGeneralAncho	= 320;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 160;
			$grilla->FColumnaFieldAncho		= 150;
		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto			= 'true';							//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Asistente modificar base retenible'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VAutoResize			= 'false';						//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 				= 400;								//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 				= 200;								//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll			= 'true';						//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';							//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';							//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Retencion','retencion',150,'true','false');
			$grilla->AddTextField('% Valor','valor',150,'true','false');
			$grilla->AddTextField('Base retenible','Base',150,'true','false');
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/**//////////////////////////////////////////////////////////////**/
	/**///				     INICIALIZACION DE LA GRILLA	  		        ///**/
	/**/															                              /**/
	/**/		$grilla->Link = $link;  			//Conexion a la BD			  /**/
	/**/		$grilla->inicializa($_POST);	//Variables POST			    /**/
	/**/		$grilla->GeneraGrilla(); 			//Inicializa la Grilla		/**/
	/**/																														/**/
	/**//////////////////////////////////////////////////////////////**/

if(!isset($opcion)){  ?>
	<script>

		function agregarBtnBuscarCuentaImpuesto(input){
			input.readOnly = true;
			input.setAttribute("style","float:left; width:105px;");

			var idInput = input.id;

			var btnBuscarCuenta = document.createElement('div');
			btnBuscarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnBuscarCuenta.setAttribute('title','Buscar cuenta');
			btnBuscarCuenta.setAttribute('onclick','ventanaBuscarCuentaImpuesto("'+idInput+'")');
			btnBuscarCuenta.innerHTML = '<img src="img/buscar20.png" />';
			document.getElementById('DIV_'+idInput).appendChild(btnBuscarCuenta);
		}

		function ventanaBuscarCuentaImpuesto(idInput){
			// var arrayId     = idInput.split('_');
			// var typeCuenta = arrayId[3];


			var typeCuenta = (idInput.search('niif') > 0)? 'niif': ''
			,	title = (typeCuenta == '')? 'Seleccione la cuenta Colgaap': 'Seleccione la cuenta Niif';

			Win_VentanaBuscarPucImpuesto = new Ext.Window({
	            width       : 600,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucImpuesto',
	            title       : title,
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'impuestos/busqueda_puc_impuesto.php',
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
						handler   : function(){ Win_VentanaBuscarPucImpuesto.close(); }
	                }
	            ]
	        }).show();
		}

		function agregarBtnSincronizarCuentaImpuesto(input){
			var idInput = input.id
			,	estado  = idInput.split('_')[1];

			var btnSincronizarCuenta = document.createElement('div');
			btnSincronizarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnSincronizarCuenta.setAttribute('id','btn_sincronizar_'+idInput);
			btnSincronizarCuenta.setAttribute('title','Sincronizar cuenta en Niif');
			btnSincronizarCuenta.innerHTML = '<img src="img/refresh.png" onclick="sincronizaCuentaImpuestoEnNiif(\''+idInput+'\')"/>';
			document.getElementById('DIV_'+idInput).appendChild(btnSincronizarCuenta);
		}

		function sincronizaCuentaImpuestoEnNiif(idInput){

			var cuenta = document.getElementById(idInput).value;
			if(isNaN(cuenta) || cuenta == 0){ alert("Aviso\nSeleccione una cuenta para sincronizar"); return; }

			Ext.get('btn_sincronizar_'+idInput).load({
				url     : 'impuestos/bd/bd.php',
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
<?php } else if($opcion == 'Vupdate' || $opcion == 'Vagregar'){  ?>
	<script>
		//===========================// VARIABLES GLOBALES //===========================//
		//******************************************************************************//
		var cuenta_compra    = document.getElementById('impuestos_items_cuenta_compra')
		,	cuenta_compra_niif = document.getElementById('impuestos_items_cuenta_compra_niif')
		,	cuenta_venta       = document.getElementById('impuestos_items_cuenta_venta')
		,	cuenta_venta_niif  = document.getElementById('impuestos_items_cuenta_venta_niif');

		var cuenta_compra_devolucion    = document.getElementById('impuestos_items_cuenta_compra_devolucion')
		,	cuenta_compra_devolucion_niif = document.getElementById('impuestos_items_cuenta_compra_devolucion_niif')
		,	cuenta_venta_devolucion       = document.getElementById('impuestos_items_cuenta_venta_devolucion')
		,	cuenta_venta_devolucion_niif  = document.getElementById('impuestos_items_cuenta_venta_devolucion_niif');

		agregarBtnBuscarCuentaImpuesto(cuenta_compra);
		agregarBtnBuscarCuentaImpuesto(cuenta_compra_niif);
		agregarBtnBuscarCuentaImpuesto(cuenta_venta);
		agregarBtnBuscarCuentaImpuesto(cuenta_venta_niif);

		agregarBtnBuscarCuentaImpuesto(cuenta_compra_devolucion);
		agregarBtnBuscarCuentaImpuesto(cuenta_compra_devolucion_niif);
		agregarBtnBuscarCuentaImpuesto(cuenta_venta_devolucion);
		agregarBtnBuscarCuentaImpuesto(cuenta_venta_devolucion_niif);

		agregarBtnSincronizarCuentaImpuesto(cuenta_compra);
		agregarBtnSincronizarCuentaImpuesto(cuenta_venta);
		agregarBtnSincronizarCuentaImpuesto(cuenta_compra_devolucion);
		agregarBtnSincronizarCuentaImpuesto(cuenta_venta_devolucion);

		var disponible_compra = document.getElementById('impuestos_items_compra')
		,	disponible_venta    = document.getElementById('impuestos_items_venta')
		,	separadores         = document.querySelectorAll('.EmpSeparador');

		updateEstadoCompraVenta();

		function updateEstadoCompraVenta(){

			if(disponible_compra.value == 'Si'){
				separadores[0].style.display = 'block';
				separadores[1].style.display = 'block';

				if(cuenta_compra.value == ' '){ cuenta_compra.value = ''; }
				if(cuenta_compra_niif.value == ' '){ cuenta_compra_niif.value = ''; }
				if(cuenta_compra_devolucion.value == ' '){ cuenta_compra_devolucion.value = ''; }
				if(cuenta_compra_devolucion_niif.value == ' '){ cuenta_compra_devolucion_niif.value = ''; }

				document.getElementById('EmpConte_impuestos_items_cuenta_compra').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_niif').style.display = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_devolucion').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_devolucion_niif').style.display = 'block';
			}
			else{
				separadores[0].style.display = 'none';
				separadores[1].style.display = 'none';

				cuenta_compra.value                 = ' ';
				cuenta_compra_niif.value            = ' ';
				cuenta_compra_devolucion.value      = ' ';
				cuenta_compra_devolucion_niif.value = ' ';

				document.getElementById('EmpConte_impuestos_items_cuenta_compra').style.display      = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_niif').style.display = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_devolucion').style.display      = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_compra_devolucion_niif').style.display = 'none';
			}

			if(disponible_venta.value == 'Si'){
				separadores[2].style.display = 'block';
				separadores[3].style.display = 'block';

				document.getElementById('EmpConte_impuestos_items_cuenta_venta').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_niif').style.display = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion_niif').style.display = 'block';

				document.getElementById('EmpConte_impuestos_items_cuenta_venta').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_niif').style.display = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion').style.display      = 'block';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion_niif').style.display = 'block';
			}
			else{
				separadores[2].style.display = 'none';
				separadores[3].style.display = 'none';

				cuenta_venta.value                 = ' ';
				cuenta_venta_niif.value            = ' ';
				cuenta_venta_devolucion.value      = ' ';
				cuenta_venta_devolucion_niif.value = ' ';

				document.getElementById('EmpConte_impuestos_items_cuenta_venta').style.display      = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_niif').style.display = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion').style.display      = 'none';
				document.getElementById('EmpConte_impuestos_items_cuenta_venta_devolucion_niif').style.display = 'none';
			}
		}

		disponible_venta.onchange  = function(){ updateEstadoCompraVenta(); }
		disponible_compra.onchange = function(){ updateEstadoCompraVenta(); }

	</script>
<?php } ?>
