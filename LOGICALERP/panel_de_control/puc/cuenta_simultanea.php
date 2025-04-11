<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");

	$id_empresa  = $_SESSION['EMPRESA'];
	$id_sucursal = $_SESSION['SUCURSAL'];

	// VALIDAR EL PLAN Y LA CANTIDAD DE SUCURSALES PERMITIDAS
	// $sql="SELECT COUNT(id) AS cont FROM empresas_sucursales WHERE activo=1 AND id_empresa=$id_empresa";
	// $query=mysql_query($sql,$link);
	// $numero_sucursales=mysql_result($query,0,'cont');

	// if ($_SESSION['PLAN_SUCURSALES']<=$numero_sucursales) {
	// 	$btnNuevo='false';
	// }
	// else{
	// 	$btnNuevo='true';
	// }

	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'cuentas_simultaneas';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'puc_cuentas_simultaneas';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND id_empresa = '$id_empresa' AND id_cuenta_principal=$id_cuenta";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 	= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 570;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 370;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 750;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto		= 210;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'cuenta,descripcion,naturaleza';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Cuenta','cuenta',100);
			$grilla->AddRow('Descripcion','descripcion',250);
			$grilla->AddRow('Tipo de movimiento','naturaleza',150);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 75;
			$grilla->FColumnaFieldAncho		= 200;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Administracion Cuentas simultaneas'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva cuenta'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresas','regresar','Win_Ventana_CtasSimultaneas.close();');
			$grilla->VAutoResize		= 'true';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 180;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho		= 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto		= 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddTextField('Codigo:','cuenta',120,'true','false');
			$grilla->AddTextField('Descripcion:','descripcion',150,'true','false');
			$grilla->AddComboBox('Movimiento','naturaleza',150,'true','false','contrapartida:Contrapartida,partida:Partida');
			$grilla->AddTextField('','id_cuenta',200,'false','hidden');
			$grilla->AddTextField('','id_cuenta_principal',200,'false','hidden',$id_cuenta);
			$grilla->AddTextField('','id_empresa',200,'false','hidden', $id_empresa);
			$grilla->AddTextField('','id_sucursal',200,'false','hidden', $id_sucursal);

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

		function ventanaBuscarPucCuentasPago(typeCuenta,campoId,campoText,campoDesc){

			var titulo=(typeCuenta=='colgaap')? 'Buscar Cuenta Colgaap' : 'Buscar Cuenta Niif' ;

			Win_VentanaBuscarPucCuentasPago = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucCuentasPago',
	            title       : 'Buscar Cuenta local',
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
			var cuenta      = document.getElementById('div_buscar_cuenta_concepto_cuenta_'+id).innerHTML
			,	descripcion = document.getElementById('div_buscar_cuenta_concepto_descripcion_'+id).innerHTML

			document.getElementById(campoId).value   = id;
			document.getElementById(campoText).value = cuenta;
			document.getElementById(campoDesc).value = descripcion;
			Win_VentanaBuscarPucCuentasPago.close();
		}

	</script>
<?php
}
else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>
	<script>
		var inputCuenta    = document.getElementById('cuentas_simultaneas_cuenta')
		,	inputDesCuenta = document.getElementById('cuentas_simultaneas_descripcion')

		inputCuenta.readOnly    = true;
		inputDesCuenta.readOnly = true;

		inputCuenta.setAttribute("style","float:left; width:135px;");

		var divBtnCuenta = document.createElement("div");
		divBtnCuenta.setAttribute("class","divBtnBuscarPuc");
		divBtnCuenta.setAttribute("onclick","ventanaBuscarPucCuentasPago('colgaap','cuentas_simultaneas_id_cuenta','cuentas_simultaneas_cuenta','cuentas_simultaneas_descripcion')");
		divBtnCuenta.setAttribute('title','Buscar Cuenta Colgaap');
		divBtnCuenta.innerHTML = '<img src="img/buscar20.png" />';
		document.getElementById("DIV_cuentas_simultaneas_cuenta").appendChild(divBtnCuenta);

    </script>

<?php } ?>