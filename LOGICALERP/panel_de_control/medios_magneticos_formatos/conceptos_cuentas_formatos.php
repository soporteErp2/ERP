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
			$grilla->GrillaName	 		= 'conceptos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'medios_magneticos_formatos_conceptos_cuentas';		//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa='$id_empresa' AND id_formato=$id_formato AND id_concepto=$id_concepto";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
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
			$grilla->CamposBusqueda		= 'descripcion';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA
			$grilla->DivActualiBusqueda = '' ;				//VARIABLE QUE DEFINE LA CAPA DONDE SE ACTUALIZA LA GRILLA DESPUES DE UNA BUSQUEDA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('Cuenta Inicial','cuenta_inicial',80);
			$grilla->AddRow('Cuenta Final','cuenta_final',80);
			$grilla->AddRow('Clasificacion','nombre_columna_formato',200);
			$grilla->AddRow('Tope','tope',80);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 310;
			$grilla->FColumnaGeneralAncho	= 300;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 120;
			$grilla->FColumnaFieldAncho		= 180;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Ventana Conceptos formato'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva cuenta'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Ventana_conceptos_cuentas.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 380;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 440;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'true';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LOS FORMULARIOS DE CAPTURA Y EDICION
			// $grilla->AddSeparator('Formato: '.$codigo_formato.' <br>Concepto: '.$concepto);

			$grilla->AddSeparator('Rango de Cuentas');

			$grilla->AddTextField('Cuenta Inicial:','cuenta_inicial',150,'true','false');
			$grilla->AddTextField('Descripcion:','descripcion_cuenta_inicial',150,'true','false');
			$grilla->AddTextField('Cuenta Inicial','id_cuenta_inicial',180,'true','true');

			$grilla->AddTextField('Cuenta Final:','cuenta_final',150,'true','false');
			$grilla->AddTextField('Descripcion:','descripcion_cuenta_final',150,'true','false');
			$grilla->AddTextField('Cuenta Final','id_cuenta_final',180,'true','true');

			$grilla->AddSeparator('Forma de calcular los valores de las  cuentas');
			$grilla->AddComboBox('Calcular por','forma_calculo',150,'true','false','suma_debitos:Suma Debitos,suma_creditos:Suma Creditos,debito_menos_credito:Debito - Credito,credito_menos_debito:Credito - Debito,saldo_actual:Saldo Actual,saldo_inicial:Saldo Inicial');

			$grilla->AddSeparator('Clasificacion en el formato');
			$grilla->AddComboBox('Columna','id_columna_formato',150,'true','true','medios_magneticos_formatos_columnas,id,nombre,true','activo=1 AND id_empresa='.$id_empresa.' AND id_formato='.$id_formato.' ORDER BY orden ASC');

			$grilla->AddSeparator('Tope minimo a declarar');
			$grilla->AddTextField('Tope','tope',180,'true','false');

			$grilla->AddValidation('tope','numero-real');

			$grilla->AddTextField('','id_empresa',200,'true','true',$id_empresa);
			$grilla->AddTextField('','id_formato',200,'true','true',$id_formato);
			$grilla->AddTextField('','id_concepto',200,'true','true',$id_concepto);


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

	</script>
<?php
}
if ($opcion=='Vupdate' || $opcion=='Vagregar') {
?>
	<script>
		// CUENTA INICIAL
		var inputCuentaInicial      = document.getElementById('conceptos_cuenta_inicial');
		var inputDescCuentaInicial      = document.getElementById('conceptos_descripcion_cuenta_inicial');
		inputCuentaInicial.readOnly = true;
		inputDescCuentaInicial.readOnly = true;

		inputCuentaInicial.setAttribute("style","float:left; width:135px;");

		var divBtnCuentaInicial = document.createElement("div");
		divBtnCuentaInicial.setAttribute("class","divBtnBuscarPuc");
		divBtnCuentaInicial.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','conceptos_id_cuenta_inicial','conceptos_cuenta_inicial','conceptos_descripcion_cuenta_inicial')");
		divBtnCuentaInicial.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnCuentaInicial.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_conceptos_cuenta_inicial").appendChild(divBtnCuentaInicial);

		// CUENTA FINAL
		var inputCuentaFinal      = document.getElementById('conceptos_cuenta_final');
		var inputDescCuentaFinal      = document.getElementById('conceptos_descripcion_cuenta_final');
		inputCuentaFinal.readOnly = true;
		inputDescCuentaFinal.readOnly = true;

		inputCuentaFinal.setAttribute("style","float:left; width:135px;");

		var divBtnCuentaInicial = document.createElement("div");
		divBtnCuentaInicial.setAttribute("class","divBtnBuscarPuc");
		divBtnCuentaInicial.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','conceptos_id_cuenta_final','conceptos_cuenta_final','conceptos_descripcion_cuenta_final')");
		divBtnCuentaInicial.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnCuentaInicial.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_conceptos_cuenta_final").appendChild(divBtnCuentaInicial);

		function ventanaBuscarPucCuentasPago(typeCuenta,campoId,campoText,campoDesc){

			Win_VentanaBuscarPucCuentasPago = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucCuentasPago',
	            title       : 'Buscar Cuenta Colgaap',
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
								opc          : 'colgaap',
								nombreGrilla : 'buscar_cuenta_concepto',
								cargaFuncion : 'renderizaResultadoVentanaBuscarCuenta(id,"'+campoId+'","'+campoText+'","'+campoDesc+'")',
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

		function renderizaResultadoVentanaBuscarCuenta(id,campoId,campoText,campoDesc){
			var cuenta = document.getElementById('div_buscar_cuenta_concepto_cuenta_'+id).innerHTML;
			var descripcion = document.getElementById('div_buscar_cuenta_concepto_descripcion_'+id).innerHTML;
			document.getElementById(campoId).value=id;
			document.getElementById(campoText).value=cuenta;
			document.getElementById(campoDesc).value=descripcion;

			Win_VentanaBuscarPucCuentasPago.close();

		}

	</script>
<?php
}
?>