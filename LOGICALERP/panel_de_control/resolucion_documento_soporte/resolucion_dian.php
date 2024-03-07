<?php
	include("../../../configuracion/conectar.php");
	include("../../../configuracion/define_variables.php");
	include("../../../misc/MyGrilla/class.MyGrilla.php");
	$id_empresa = $_SESSION['EMPRESA'];

	if ($opcion=='Vupdate') {
		$sql ="SELECT consecutivo_factura,numero_final_resolucion FROM ventas_facturas_configuracion WHERE activo=1 AND id=$id";
		$query = $mysql->query($sql,$mysql->link);
		$consecutivo = $mysql->result($query,0,'consecutivo_factura' );
		$numero_final = $mysql->result($query,0,'numero_final_resolucion' );
		if ($consecutivo>$numero_final) {
			$script = "Ext.getCmp('BtnV_Resolucion').disable();
						Ext.getCmp('BtnV_eliminar_Resolucion').disable();
						document.getElementById('Resolucion_consecutivo_resolucion').disabled=true;
						document.getElementById('Resolucion_fecha_resolucion').disabled=true;
						document.getElementById('Resolucion_numero_inicial_resolucion').disabled=true;
						document.getElementById('Resolucion_numero_final_resolucion').disabled=true;
						document.getElementById('Resolucion_tipo').disabled=true;
						document.getElementById('Resolucion_consecutivo_factura').disabled=true;
						document.getElementById('Resolucion_digitos').disabled=true;
						document.getElementById('Resolucion_prefijo').disabled=true;

						";
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
			$grilla->GrillaName	 		= 'Resolucion';  	//NOMBRE DE LA GRILLA (DEBE SER UNICO POR CADA GRILLA DE LA APLICACION)
		//QUERY
			$grilla->TableName			= 'resolucion_documento_soporte';			//NOMBRE DE LA TABLA EN LA BASE DE DATOS
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
			$grilla->CamposBusqueda		= 'nombre,id';		//VARIABLE QUE DEFINE LOS CAMPOS DE LA BD DONDE SE BUSCARA

		//CONFIGURACION DE CAMPOS EN LA GRILLA
			// $grilla->AddRow('Codigo','id',60,'codigo'); //(Nombre Columna, Nombre del Campo, Largo de la Celda, Funcion PHP definida dentro de la clase para dar formato)
			$grilla->AddRow('Numero','numero_resolucion',100);
			$grilla->AddRow('Fecha res.','fecha_resolucion',100);
			$grilla->AddRow('Fecha Inicial','fecha_inicio_resolucion',100);
			$grilla->AddRow('Fecha Final','fecha_final_resolucion',100);
			$grilla->AddRow('Prefijo','prefijo',100);
			$grilla->AddRow('No. Inicial','numero_inicial_resolucion',100);
			$grilla->AddRow('No. Final','numero_final_resolucion',100);
			$grilla->AddRowImage('Asignar Sucursales','<center><img onClick="configuracionSucursales([id],\'[numero_resolucion]\')" src="../../temas/clasico/images/BotonesTabs/sucursales16.png" style="cursor:hand;" title="Configure que sucursales usaran esta resolucion" ></center>',105)	;
			$grilla->AddRow('Consecutivo','consecutivo',100);

		//CONFIGURACION FORMULARIO
			$grilla->FContenedorAncho		= 300;
			$grilla->FColumnaGeneralAncho	= 290;
			$grilla->FColumnaGeneralAlto	= 25;
			$grilla->FColumnaLabelAncho		= 110;
			$grilla->FColumnaFieldAncho		= 130;

		//CONFIGURACION DE LA VENTANA DE INSERT, UPDATE Y DELETE
			$grilla->VentanaAuto		= 'true';			//SI LA VENTANA DE INSERT, UPDATE Y DELETE ES AUTOMATICA O MANUAL
			$grilla->TituloVentana	= 'Administracion Resolucion'; //NOMBRE DE LA VENTANA DE INSER, UPDATE Y DELETE
			$grilla->VBarraBotones  = 'true';			        //SI HAY O NO BARRA DE BOTONES
			$grilla->VBotonNuevo		= 'true';			        //SI LLEVA EL BOTON DE AGREGAR REGISTRO
			$grilla->VBotonNText		= 'Nueva Resolucion'; //TEXTO DEL BOTON DE NUEVO REGISTRO
			$grilla->VBotonNImage		= 'addsucursal';			//IMAGEN CSS DEL BOTON
			$grilla->AddBotton('Regresar','regresar','Win_Panel_Global.close();');
			$grilla->VAutoResize		 = 'false';		//SI LA VENTANA ES AUTORESIZABLE (LIQUIDA) -> "true" SI NO -> "false"
			$grilla->VAncho		 		   = 330;				//ANCHO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAlto		 		   = 430;				//ALTO DE LA VENTANA -- SOLO FUNCIONA CUANDO VAutoResize = 'false'
			$grilla->VAutoScroll		 = 'false';		//SI LA VENTANA TIENE O NO AUTOSCROLL
			$grilla->VBotonEliminar	 = 'true';		//SI MUESTRA BOTON DE ELIMINAR EN LA VENTANA DE EDICION
			$grilla->VComporEliminar = 'true';		//COMPORTAMIENTO DEL BOTON DE ELIMINAR ("true" ES CAMPO ACTIVO DE 1 A 0) ("false" -> ELIMINA EL REGISTRO DE LA BASE DE DATOS)
			$grilla->MenuContext     = 'true';

		//CONFIGURACION DE LO FORMULARIOS DE CAPTURA Y EDICION
			$grilla->AddSeparator('Informacion resolucion');
			$grilla->AddTextField('Numero','numero_resolucion',150,'true','false');
			$grilla->AddTextField('Fecha Resolucion','fecha_resolucion',150,'true','false');
			$grilla->AddTextField('Fecha Inicial','fecha_inicio_resolucion',150,'true','false');
			$grilla->AddTextField('Fecha Final','fecha_final_resolucion',150,'true','false');
			$grilla->AddTextField('Prefijo','prefijo',150,'false','false');
			$grilla->AddTextField('No. Inicial','numero_inicial_resolucion',150,'true','false');
			$grilla->AddTextField('No. Final','numero_final_resolucion',150,'true','false');
			// $grilla->AddTextField('Llave Tecnica','llave_tecnica',150,'true','false');
   			// $grilla->AddComboBox('Tipo','tipo',150,'true','false','FC:Factura Por Computador,FE:Factura Electronica,FM:Factura Manual');
			$grilla->AddSeparator('Configuracion Consecutivo ');
			$grilla->AddTextField('Consecutivo','consecutivo',150,'true','false');
			$grilla->AddTextField('Empresa','id_empresa',200,'false','hidden', $id_empresa);

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
		function configuracionSucursales(id_resolucion,num_resolucion){
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
			        url     : 'resolucion_documento_soporte/configurar_sucursal.php',
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
		new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'Resolucion_fecha_resolucion',
	    editable   : false,                 //EDITABLE
	    listeners  : { select: function() {   } }
		});

		new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'Resolucion_fecha_inicio_resolucion',
	    editable   : false,                 //EDITABLE
	    listeners  : { select: function() {   } }
		});

		new Ext.form.DateField({
	    format     : 'Y-m-d',               //FORMATO
	    width      : 150,                   //ANCHO
	    allowBlank : false,
	    showToday  : false,
	    applyTo    : 'Resolucion_fecha_final_resolucion',
	    editable   : false,                 //EDITABLE
	    listeners  : { select: function() {   } }
		});
  </script>
<?php } ?>
