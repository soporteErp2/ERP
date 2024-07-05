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

	$id_empresa = $_SESSION['EMPRESA'];
	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'cuentasPago';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'configuracion_cuentas_pago';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = $id_empresa";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 760;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 310;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'nombre,cuenta,tipo';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Id','id',25);
			$grilla->AddRowImage('','<center><div style="float:left; margin: 0 0 0 7px" onclick="ventanaCuentaPagoTercero([id],\'[tipo_tercero]\')"><img src="cuentas_pago/img/[tipo_tercero].png" style="cursor:pointer" width="16" height="16"></div></center>',35);
			$grilla->AddRow('Nombre','nombre',200);
			$grilla->AddRow('Cuenta Colgaap','cuenta',100);
			$grilla->AddRow('Cuenta Niif','cuenta_niif',100);
			$grilla->AddRow('Modulo','tipo',80);
			$grilla->AddRow('Estado','estado',80);
			$grilla->AddRow('Sucursal','sucursal',120);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 310;
			$grilla->FColumnaGeneralAncho	= 310;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 100;
			$grilla->FColumnaFieldAncho		= 190;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Cuentas de pago'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva<br/>Cuenta de Pago'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 240;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Nombre','nombre',150,'true','false');
			$grilla->AddComboBox('Modulo','tipo',150,'true','false','Venta:Venta,Compra:Compra');
			$grilla->AddComboBox('Estado','estado',150,'true','false','Contado:Contado,Credito:Credito');
			$grilla->AddTextField('Cuenta Colgaap','cuenta',150,'true','false');
			$grilla->AddTextField('Cuenta Niif','cuenta_niif',150,'true','false');
			$grilla->AddTextField('','id_empresa',150,'true','true',$id_empresa);
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


	/**//////////////////////////////////////////////////////////////**/
	/**///				INICIALIZACION DE LA GRILLA	  			  ///**/
	/**/															/**/
	/**/	$grilla->Link = $link;  	//Conexion a la BD			/**/
	/**/	$grilla->inicializa($_POST);//variables POST			/**/
	/**/	$grilla->GeneraGrilla(); 	// Inicializa la Grilla		/**/
	/**/															/**/
	/**//////////////////////////////////////////////////////////////**/


if($opcion=='Vupdate' || $opcion=='Vagregar'){ ?>

	<script>

		codigo_puc      = document.getElementById("cuentasPago_cuenta");
		tipo_factura    = document.getElementById("cuentasPago_tipo");
		codigo_puc_niif = document.getElementById("cuentasPago_cuenta_niif");

		codigo_puc.readOnly      = true;
		codigo_puc_niif.readOnly = true;

		//BOTON CUENTA COMPRA
		codigo_puc.setAttribute("style","float:left; width:108px;");
		codigo_puc_niif.setAttribute("style","float:left; width:108px;");

		//UPDATE CUENTA CAMBIO TIPO FACTURA
		tipo_factura.setAttribute("onchange","ValidarFieldVacio(this); updateCampoCuenta(); verificaTipoDocumento();");

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap')");
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_cuentasPago_cuenta").appendChild(divBtnPlantilla);

		//SINCRONIZAR CUENTA NIIF
		var divBtnSincroniza = document.createElement('div');
		divBtnSincroniza.setAttribute('class','divBtnBuscarPuc');
		divBtnSincroniza.setAttribute('id','btn_sincronizar_niif_pago');
		divBtnSincroniza.setAttribute('title','Homologar cuenta niif');
		divBtnSincroniza.innerHTML = '<img src="img/refresh.png" onclick="sincronizaPucPagoNiif()"/>';
		document.getElementById('DIV_cuentasPago_cuenta').appendChild(divBtnSincroniza);

		var divBtnPlantilla = document.createElement("div");
		divBtnPlantilla.setAttribute("class","divBtnBuscarPuc");
		divBtnPlantilla.setAttribute("onclick","ventanaBuscarPucCuentasPago('niif')");
		divBtnPlantilla.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_cuentasPago_cuenta_niif").appendChild(divBtnPlantilla);

		function sincronizaPucPagoNiif(){
			var cuenta = codigo_puc.value;
			if(isNaN(cuenta) || cuenta == 0){ alert("Aviso\nSeleccione una cuenta para sincronizar"); return; }

			Ext.get('btn_sincronizar_niif_pago').load({
				url     : 'cuentas_pago/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc    : 'sincronizaPucPagoNiif',
					cuenta : cuenta
				}
			});
		}


		//FUNCION PARA OLCULTAR CAMPOS NO NECESARIOS PARA EL DOCUMENTO NOMINA
		function verificaTipoDocumento() {
			var estado = document.getElementById('cuentasPago_estado');
			if(tipo_factura.value=='Nomina') {
				estado.parentNode.parentNode.style.display='none';
				estado[0].value="0";
			}
			else{
				estado.parentNode.parentNode.style.display='block';
				estado[0].value="";
			}
		}

	</script>

<?php }

else if(!isset($opcion)){  ?>
	<script>
		document.getElementById("ContenedorPrincipal_cuentasPago").setAttribute("style","float:left; margin-top:10px;");

		function updateCampoCuenta(){ document.getElementById('cuentasPago_cuenta').value = ''; }

		function ventanaBuscarPucCuentasPago(typeCuenta){
			var tipoDocumento = document.getElementById('cuentasPago_tipo').value;

			if(tipoDocumento == ''){ alert('Aviso,\nSeleccione el tipo de documento antes de editar el campo cuenta!'); return; }

			Win_VentanaBuscarPucCuentasPago = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucCuentasPago',
	            title       : 'Cuentas Puc',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'cuentas_pago/busqueda_puc_cuenta_pago.php',
	                scripts : true,
	                nocache : true,
	                params  : { tipoDocumento : tipoDocumento, typeCuenta : typeCuenta }
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

		function ventanaCuentaPagoTercero(id,tipoTercero){
			if(tipoTercero != 'true'){ return; }

			var title = document.getElementById('div_cuentasPago_nombre_'+id).innerHTML;

			Win_Ventana_Tercero_cuenta_pago = new Ext.Window({
			    width       : 300,
			    height      : 200,
			    id          : 'Win_Ventana_Tercero_cuenta_pago',
			    title       : title,
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'cuentas_pago/bd/bd.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			        	opc : 'ventanaCuentaPagoTercero',
			            idCuentaPago : id,
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
			                    text        : 'Guardar',
			                    scale       : 'large',
			                    iconCls     : 'guardar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); guardarTerceroCuentaPago(id) }
			                },
			                {
			                    xtype       : 'button',
			                    id 			: 'Btn_eliminar_tercero_cuenta_pago',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Eliminar',
			                    scale       : 'large',
			                    iconCls     : 'eliminar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); eliminarTerceroCuentaPago(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function buscarTerceroCuentaPago(){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_Buscar_tercero_cuenta_pago = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_Buscar_tercero_cuenta_pago',
			    title       : 'Seleccione el Tercero',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/busquedaTerceros.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						nombre_grilla : 'Buscar_tercero_cuenta_pago',
						cargaFuncion  : 'response_buscar_tercero_cuenta_pago(id);',
						quitarHeight  : 200,
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
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'top',
			                    hidden      : false,
			                    handler     : function(){ BloqBtn(this); Win_Ventana_Buscar_tercero_cuenta_pago.close(id); }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function response_buscar_tercero_cuenta_pago(id){
			var nombre = document.getElementById('div_Buscar_tercero_cuenta_pago_nombre_'+id).innerHTML
			,	nit    = document.getElementById('div_Buscar_tercero_cuenta_pago_numero_identificacion_'+id).innerHTML;

			document.getElementById('inputIdTerceroCuentaPago').value  = id;
			document.getElementById('inputNitTerceroCuentaPago').value = nit;
			document.getElementById('inputTerceroCuentaPago').value    = nombre;

			Win_Ventana_Buscar_tercero_cuenta_pago.close(id)
		}

		function guardarTerceroCuentaPago(id){
			var idTercero = document.getElementById('inputIdTerceroCuentaPago').value;
			if(idTercero == 0 || isNaN(idTercero)){ return; }

			Ext.get('loadSaveTerceroCuentaPago').load({
				url     : 'cuentas_pago/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc          : 'guardarTerceroCuentaPago',
					idCuentaPago : id,
					idTercero    : idTercero
				}
			});
		}

		function eliminarTerceroCuentaPago(id){
			Ext.get('loadSaveTerceroCuentaPago').load({
				url     : 'cuentas_pago/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					opc          : 'eliminarTerceroCuentaPago',
					idCuentaPago : id,
				}
			});
		}
	</script>
<?php
} ?>