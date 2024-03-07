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
	$arrayTypeCuenta['Compra'] = "PRECIO:PRECIO,IMPUESTO:IMPUESTO,CONTRAPARTIDA PRECIO:CONTRAPARTIDA PRECIO";
	$arrayTypeCuenta['Venta']  = "COSTO:COSTO,CONTRAPARTIDA COSTO:CONTRAPARTIDA COSTO,PRECIO:PRECIO,IMPUESTO:IMPUESTO,CONTRAPARTIDA PRECIO:CONTRAPARTIDA PRECIO";

	//CONULTAR LAS CUENTAS QUE YA ESTAN PARA NO REPETIRLAS EN LA INTERFAZ
	$sql="SELECT descripcion FROM plantillas_configuracion WHERE activo = 1 AND plantillas_id = '$idPlantilla'";
	$query=mysql_query($sql,$link);
	while ($row=mysql_fetch_array($query)) {
		// echo $row['descripcion']."<br>";
		$arrayTypeCuenta['Compra'] = str_replace($row['descripcion'].":".$row['descripcion'].",","",$arrayTypeCuenta['Compra']);
		$arrayTypeCuenta['Compra'] = str_replace($row['descripcion'].":".$row['descripcion'],"",$arrayTypeCuenta['Compra']);

		$arrayTypeCuenta['Venta']  = str_replace($row['descripcion'].":".$row['descripcion'].",","",$arrayTypeCuenta['Venta']);
		$arrayTypeCuenta['Venta']  = str_replace($row['descripcion'].":".$row['descripcion'],"",$arrayTypeCuenta['Venta']);
	}
	// echo $arrayTypeCuenta['Compra'].'<br>';
	// echo $arrayTypeCuenta['Venta'] ;
//Win_Agregar_Cuenta.close();

	if ( $arrayTypeCuenta[$estadoCuenta]=="") {
		$script='alert("Aviso\nYa se configuraron todas las cuentas");Win_Agregar_configPlantilla.close();';
		$arrayTypeCuenta[$estadoCuenta]=":Configuracion Completa!";
	}

	if ($opcion == 'Vupdate') {
		$sql="SELECT descripcion  FROM plantillas_configuracion WHERE activo = 1 AND plantillas_id = '$idPlantilla' AND id=$id";
		$query=mysql_query($sql,$link);
		$cuentaConfig=mysql_result($query,0,'descripcion');
		if ($arrayTypeCuenta[$estadoCuenta]==":Configuracion Completa!") {
			$arrayTypeCuenta[$estadoCuenta]=$cuentaConfig.':'.$cuentaConfig;
		}else{
			$arrayTypeCuenta[$estadoCuenta]=($arrayTypeCuenta[$estadoCuenta]!="")? $arrayTypeCuenta[$estadoCuenta].','.$cuentaConfig.':'.$cuentaConfig : $cuentaConfig.':'.$cuentaConfig ;
		}
	}

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'configPlantilla';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'plantillas_configuracion';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= "activo = 1 AND plantillas_id = '$idPlantilla'";		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,100';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			$grilla->AutoResize	 		= 'false';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 660;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 310;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			// $grilla->QuitarAncho		= 80;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			// $grilla->QuitarAlto		= 170;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'codigo_puc,descripcion,porcentaje';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			$grilla->AddRow('descripcion','descripcion',150);
			$grilla->AddRow('PUC','codigo_puc',70);
			$grilla->AddRow('Cuenta','cuenta',280);
			$grilla->AddRow('%','porcentaje',60);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho     = 300;
			$grilla->FColumnaGeneralAncho = 290;
			$grilla->FColumnaGeneralAlto  = 25;
			$grilla->FColumnaLabelAncho   = 80;
			$grilla->FColumnaFieldAncho   = 150;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto     = 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana   = 'Cuenta';		//NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones   = 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo     = 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText     = 'Nueva Cuenta'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage    = 'add';			//IMAGEN CSS DEL BOTON
			$grilla->VAutoResize     = 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho          = 300;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto           = 210;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VQuitarAncho    = 70;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VQuitarAlto     = 160;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'true'
			$grilla->VAutoScroll     = 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar  = 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
 			$grilla->AddBotton('Regresar','regresar','Win_Ventana_configPlantillas.close(); Actualiza_Div_panelControlPlantillas("'.$idPlantilla.'")');

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddComboBox('Tipo','descripcion',170,'true','false',$arrayTypeCuenta[$estadoCuenta]);
			$grilla->AddTextField('','plantillas_id',170,'true','true',$idPlantilla);
			$grilla->AddTextField('Cuenta Colgaap','codigo_puc',170,'true','false');
			$grilla->AddTextField('Cuenta Niif','codigo_niif',170,'true','false');

			// $grilla->AddComboBox('Caracter','caracter',170,'true','false','Debito:Debito,Credito:Credito');
			$grilla->AddTextField('%','porcentaje',170,'true','false');

			$grilla->AddValidation('codigo_puc','numero');
			$grilla->AddValidation('porcentaje','numero-real');

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

		function ventanaBuscarPucPlantilla(){

			Win_VentanaBuscarPucPlantilla = new Ext.Window({
	            width       : 600,
	            height      : 500,
	            id          : 'Win_VentanaBuscarPucPlantilla',
	            title       : 'Proveedores',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'plantillas/busqueda_puc_plantilla.php',
	                scripts : true,
	                nocache : true,
	                params  : { }
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarPucPlantilla.close(); }
	                }
	            ]
	        }).show();
		}

		function hiddenCampoImpuesto(campoSelect){
			if(campoSelect.value == 'IMPUESTO'){ document.getElementById('EmpConte_configPlantilla_porcentaje').style.display='block'; }
			else{
				document.getElementById('EmpConte_configPlantilla_porcentaje').value         = '0';
				document.getElementById('EmpConte_configPlantilla_porcentaje').style.display = 'none';
			}
		}

		function ventanaBuscarCuentaPlantilla(opc,typeVentana,idInput){
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			title       = (typeVentana == 'niif')? 'Seleccione la cuenta Niif': 'Seleccione la cuenta Colgaap';
			typeVentana = (typeVentana == 'niif')? '_niif': '';

			Win_Ventana_Buscar_cuenta_plantilla = new Ext.Window({
			    width       : 610,
			    height      : 480,
			    id          : 'Win_Ventana_Buscar_cuenta_plantilla',
			    title       : title,
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'plantillas/buscar_cuenta.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						idInput     : idInput,
						typeVentana : typeVentana,
			        }
			    },
			    tbar        :
			    [
			        {
			            xtype   : 'buttongroup',
			            columns : 3,
			            title   : 'Opciones',
			            items   :
			            [
			                {
			                    xtype       : 'button',
			                    width       : 60,
			                    height      : 56,
			                    text        : 'Regresar',
			                    scale       : 'large',
			                    iconCls     : 'regresar',
			                    iconAlign   : 'left',
			                    handler     : function(){ Win_Ventana_Buscar_cuenta_plantilla.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		function sincronizaCuentaEnNiifPlantilla(estado,idInput){
			var cuenta = document.getElementById(idInput).value;

			if(isNaN(cuenta) || cuenta < 100000){ alert('Aviso\nSeleccione un numero de cuenta Colgaap valido!'); return; }
			Ext.get('btn_sincroniza_'+estado).load({
				url     : 'plantillas/bd/bd.php',
				scripts : true,
				nocache : true,
				params  :
				{
					op      : 'sincronizaCuentaNiif',
					estado  : estado,
					cuenta  : cuenta,
					idInput : idInput,
				}
			});
		}

	</script>
<?php }

else if($opcion =='Vupdate' || $opcion == 'Vagregar'){  ?>

	<script>

		if ('<?php echo $opcion; ?>'=='Vagregar') { <?php echo $script; ?>}
		// if ('<?php echo $opcion; ?>'=='Vupdate') { }

		if (document.getElementById('configPlantilla_descripcion')) {
			selectDescripcion = document.getElementById('configPlantilla_descripcion');
			selectDescripcion.setAttribute("onchange","hiddenCampoImpuesto(this)");
			hiddenCampoImpuesto(selectDescripcion);
		}

		if (document.getElementById('configPlantilla_codigo_puc')) {
			input_colgaap = document.getElementById('configPlantilla_codigo_puc');
			agregarBtnBuscarCuenta(input_colgaap);
			agregarBtnSincronizarCuenta(input_colgaap);
		}

		if (document.getElementById('configPlantilla_codigo_niif')) {
			input_niif = document.getElementById('configPlantilla_codigo_niif');
			agregarBtnBuscarCuenta(input_niif);
		}

		function agregarBtnBuscarCuenta(input){
			input.readOnly = true;
			input.setAttribute("style","float:left; width:125px;");

			var idInput     = input.id;
			var arrayId     = idInput.split('_');
			var opcion      = arrayId[1];
			var typeVentana = arrayId[2];

			var btnBuscarCuenta = document.createElement('div');
			btnBuscarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnBuscarCuenta.setAttribute('title','Buscar cuenta');
			btnBuscarCuenta.setAttribute('onclick','ventanaBuscarCuentaPlantilla("'+opcion+'","'+typeVentana+'","'+idInput+'")');
			btnBuscarCuenta.innerHTML = '<img src="img/buscar20.png" />';
			document.getElementById('DIV_'+idInput).appendChild(btnBuscarCuenta);
		}

		function agregarBtnSincronizarCuenta(input){
			var idInput = input.id
			,	estado  = idInput.split('_')[1];

			var btnSincronizarCuenta = document.createElement('div');
			btnSincronizarCuenta.setAttribute('class','divBtnBuscarPuc');
			btnSincronizarCuenta.setAttribute('id','btn_sincroniza_'+estado);
			btnSincronizarCuenta.setAttribute('title','Sincronizar cuenta en niif');
			btnSincronizarCuenta.innerHTML = '<img src="img/refresh.png" onclick="sincronizaCuentaEnNiifPlantilla(\''+estado+'\',\''+idInput+'\')"/>';
			document.getElementById('DIV_'+idInput).appendChild(btnSincronizarCuenta);
		}

		if ('<?php echo $opcion; ?>'=='Vagregar') { if(document.getElementById('configPlantilla_porcentaje')){document.getElementById('configPlantilla_porcentaje').value='0';} }
	</script>
<?php
} ?>
