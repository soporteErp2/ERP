<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	$id_empresa = $_SESSION['EMPRESA'];
	$disabledConfig = 'false';

	if ($opcion=='Vupdate') {
		$sql ="SELECT consecutivo_pos,consecutivo_final FROM ventas_pos_configuracion WHERE activo=1 AND id=$id";
		$query = $mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo_pos' );
		$numero_final = $mysql->result($query,0,'consecutivo_final' );
		if ($consecutivo>$numero_final) {
			$disabledConfig = 'true';
			echo "<center><b>Resolucion utilizada por completo</b></center>";
		}
	}


	/**//////////////////////////////////////////////**/
	/**///		 INICIALIZACION DE LA CLASE  	  ///**/
	/**/											/**/
	/**/	$grilla = new MyGrilla();				/**/
	/**/											/**/
	/**//////////////////////////////////////////////**/

	//CONFIGURACION//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		//NOMBRE DE LA GRILLA
			$grilla->GrillaName	 		= 'configuracion_pos';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'ventas_pos_configuracion';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
			$grilla->MyWhere			= 'activo = 1 AND id_empresa = '.$id_empresa.' ';		//WHERE DE LA CONSULTA A LA TABLA "$TableName"
			$grilla->MySqlLimit			= '0,50';			//LIMITE DE LA CONSULTA

		//TAMANO DE LA GRILLA
			//$grilla->AutoResize	 		= 'true';			//SI LA GRILLA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->Ancho		 		= 670;				//ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			$grilla->Alto		 		= 265;				//ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'false'
			//$grilla->QuitarAncho		= 900;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ANCHO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'
			//$grilla->QuitarAlto			= 300;				//AJUSTE EN PIXELES QUE SE LE DECUENTAN AL ALTO DE LA GRILLA -- SOLO FUNCIONA CUANDO AutoResize = 'true'

		//TOOLBAR Y CAMPO DE BUSQUEDA
			$grilla->Gtoolbar			= 'true';			//SI LA GRILLA LLEVA EL TOOLBAR DE BUSQUEDA
			$grilla->CamposBusqueda		= 'numero_resolucion_dian,prefijo,consecutivo_inicial,consecutivo_final,cantidad_consecutivos,consecutivo_pos,cuenta_por_cobrar_colgaap,cuenta_por_cobrar_niif,documento_tercero,tercero';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Numero','numero_resolucion_dian',100);
			$grilla->AddRow('Fecha','fecha_resolucion_dian',80);
			$grilla->AddRow('Prefijo','prefijo',80);
			$grilla->AddRow('No. Inicial','consecutivo_inicial',80);
			$grilla->AddRow('No. Final','consecutivo_final',80);
			$grilla->AddRowImage('Asignar Sucursales','<center><img onClick="configuracionSucursalesPos([id],\'[numero_resolucion_dian]\')" src="../../temas/clasico/images/BotonesTabs/sucursales16.png" style="cursor:hand;" title="Configure que sucursales usaran esta resolucion" ></center>',105)	;
			$grilla->AddRow('Consecutivo','consecutivo_pos',100);
			// $grilla->AddRow('Digitos','digitos',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 130;
			$grilla->FColumnaFieldAncho		= 160;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana		= 'Configuracion requerida POS'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones		= 'true';			//SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			//SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'add_new';			//IMAGEN CSS DEL BOTON

			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		= 'false';			//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		= 340;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		= 480;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAutoScroll		= 'false';			//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar		= 'true';			//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar	= 'true';			//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			/*Function AddTextField(
								"label del TextField",
								"Nombre del TextField en la Base de Datos"
								"Largo del TextField"
								"Si es Obligatorio (booleano)"
								"Si es Oculto (booleano)"
						  );

			Function AddComboBox(
								"label del ComboBox",
								"Nombre del ComboBox en la Base de Datos"
								"Largo del ComboBox"
								"Si es Obligatorio (booleano)"
								"Si los datos del combo son de una Base de datos (booleano)"
								"array con los datos del combo"
						  );
			*/

			$grilla->AddSeparator('Informacion resolucion');
			$grilla->AddTextField('Numero','numero_resolucion_dian',150,'true','false');
			$grilla->AddTextField('Fecha','fecha_resolucion_dian',150,'true','false');
			$grilla->AddTextField('Prefijo','prefijo',150,'false','false');
			$grilla->AddTextField('Vigencia (meses)','vigencia',150,'true','false');
			$grilla->AddComboBox('Grandes contribuyentes','grandes_contribuyentes',150, 'true', 'false', 'si:Si, no:No');
			$grilla->AddTextField('No. Inicial','consecutivo_inicial',150,'true','false');
			$grilla->AddTextField('No. Final','consecutivo_final',150,'true','false');
   			// $grilla->AddComboBox('Tipo','tipo',150,'true','false','FC:Factura Por Computador,FE:Factura Electronica,FM:Factura Manual');

			$grilla->AddSeparator('Configuracion Consecutivo ');
			$grilla->AddTextField('Consecutivo POS','consecutivo_pos',150,'true','false');
			// $grilla->AddTextField('Digitos','digitos',150,'true','false');

			$grilla->AddSeparator('Configuracion Contable ');
			$grilla->AddTextField('Cuenta Pago','descripcion_cuenta_cobro',150,'false','false');
			$grilla->AddTextField('Tercero','tercero',150,'false','false');

			$grilla->AddTextField("Configuracion cuenta pago","id_configuracion_cuenta_cobro", 200,"false","hidden");
			$grilla->AddTextField("Configuracion cuenta pago","cuenta_por_cobrar_colgaap", 200,"false","hidden");
			$grilla->AddTextField("Configuracion cuenta pago","cuenta_por_cobrar_niif", 200,"false","hidden");
			$grilla->AddTextField("Tercero id.","id_tercero", 200,"false","hidden");
			$grilla->AddTextField("Tercero Doc.","documento_tercero", 200,"false","hidden");

			$grilla->AddTextField('Empresa','id_empresa',200,'false','hidden', $id_empresa);
			// $grilla->AddTextField('Sucursal','id_sucursal',200,'false','hidden', $filtro_sucursal);
			$grilla->AddTextField('Id usuario','id_usuario',200,'false','hidden', $_SESSION['IDUSUARIO']);
			$grilla->AddTextField('Usuario','usuario',200,'false','hidden', $_SESSION['NOMBREUSUARIO']);

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

		var ventanaBuscarCuentasPago = () => {
			Win_VentanaBuscarCuentasPago = new Ext.Window({
	            width       : 680,
	            height      : 500,
	            id          : 'Win_VentanaBuscarCuentasPago',
	            title       : 'Cuentas de Pago',
	            modal       : true,
	            autoScroll  : false,
	            closable    : false,
	            autoDestroy : true,
	            autoLoad    :
	            {
	                url     : 'configuracion_pos/busqueda_cuenta_pago.php',
	                scripts : true,
	                nocache : true,
	                params  : {  }
	            },
	            tbar        :
	            [
	                {
						xtype     : 'button',
						text      : 'Regresar',
						scale     : 'large',
						iconCls   : 'regresar',
						iconAlign : 'top',
						handler   : function(){ Win_VentanaBuscarCuentasPago.close(); }
	                }
	            ]
	        }).show();
		}

		var ventanaBuscarTerceros = () => {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_terceros = new Ext.Window({
			    width       : myancho-100,
			    height      : myalto-50,
			    id          : 'Win_Ventana_terceros',
			    title       : 'Buscar tercero',
			    modal       : true,
			    autoScroll  : false,
			    closable    : false,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : '../funciones_globales/grillas/BusquedaTerceros.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
			            cargaFuncion : "renderVentanaTerceros(id)",
			            nombre_grilla : "terceros"
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
			                    handler     : function(){ BloqBtn(this); Win_Ventana_terceros.close(id) }
			                }
			            ]
			        }
			    ]
			}).show();
		}

		var renderVentanaTerceros = (id) => {
			var documento = document.getElementById(`div_terceros_numero_identificacion_${id}`).innerHTML
			,	nombre    = document.getElementById(`div_terceros_nombre_comercial_${id}`).innerHTML

			document.getElementById('configuracion_pos_id_tercero').value        = id;
			document.getElementById('configuracion_pos_tercero').value           = nombre;
			document.getElementById('configuracion_pos_documento_tercero').value = documento;

			Win_Ventana_terceros.close(id)
		}

		var configuracionSucursalesPos = (id_resolucion,num_resolucion) => {
			var myalto  = Ext.getBody().getHeight();
			var myancho = Ext.getBody().getWidth();

			Win_Ventana_config_sucursales = new Ext.Window({
			    width       : 650,
			    height      : 500,
			    id          : 'Win_Ventana_config_sucursales',
			    title       : 'Asignar Resolucion a Sucursales',
			    modal       : true,
			    autoScroll  : false,
			    closable    : true,
			    autoDestroy : true,
			    autoLoad    :
			    {
			        url     : 'configuracion_pos/configurar_sucursal.php',
			        scripts : true,
			        nocache : true,
			        params  :
			        {
						id_resolucion  : id_resolucion,
						num_resolucion : num_resolucion,
			        }
			    }
			}).show();
		}

    </script>

<?php
}
else if ($opcion=='Vagregar' || $opcion=='Vupdate') {?>
	<script>
		<?php echo $script; ?>

		//SI LA RESOLUCION ESTA HABILITADA NO BLOQUEARLA
		if ('<?php echo $disabledConfig ?>'=='false') {
			new Ext.form.DateField({
			    format     : 'Y-m-d',               //FORMATO
			    width      : 150,                   //ANCHO
			    allowBlank : false,
			    showToday  : false,
			    applyTo    : 'configuracion_pos_fecha_resolucion_dian',
			    editable   : false,                 //EDITABLE
			    listeners  : { select: function() {   } }
			});

			var inputCP      = document.getElementById('configuracion_pos_descripcion_cuenta_cobro')
			,	inputTercero = document.getElementById('configuracion_pos_tercero')

			inputCP.readOnly      = true;
			inputTercero.readOnly = true;

			inputCP.setAttribute("style","float:left; width:135px;");
			inputTercero.setAttribute("style","float:left; width:135px;");

			var divBtnCuentaPago = document.createElement("div");
			divBtnCuentaPago.setAttribute("class","divBtnBuscarPuc");
			divBtnCuentaPago.setAttribute("onclick","ventanaBuscarCuentasPago()");
			divBtnCuentaPago.setAttribute('title','Buscar Cuenta de pago');
			divBtnCuentaPago.innerHTML = '<img src="img/buscar20.png" />';
			document.getElementById("DIV_configuracion_pos_descripcion_cuenta_cobro").appendChild(divBtnCuentaPago);

			var divBtnTercero = document.createElement("div");
			divBtnTercero.setAttribute("class","divBtnBuscarPuc");
			divBtnTercero.setAttribute("onclick","ventanaBuscarTerceros() ");
			divBtnTercero.setAttribute('title','Buscar Tercero');
			divBtnTercero.innerHTML = '<img src="img/buscar20.png" />';
			document.getElementById("DIV_configuracion_pos_tercero").appendChild(divBtnTercero);
		}
		//SI LA RESOLUCION SE ACABO, ENTONCES NO SE PERMITE MODIFICAR EL REGISTRO
		else{
			document.getElementById('configuracion_pos_numero_resolucion_dian').readOnly   = true;
			document.getElementById('configuracion_pos_fecha_resolucion_dian').readOnly    = true;
			document.getElementById('configuracion_pos_prefijo').readOnly                  = true;
			document.getElementById('configuracion_pos_consecutivo_inicial').readOnly      = true;
			document.getElementById('configuracion_pos_consecutivo_final').readOnly        = true;
			document.getElementById('configuracion_pos_consecutivo_pos').readOnly          = true;
			document.getElementById('configuracion_pos_descripcion_cuenta_cobro').readOnly = true;
			document.getElementById('configuracion_pos_tercero').readOnly                  = true;
		}

    </script>
<?php } ?>
